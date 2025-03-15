<?php
namespace Modules\Cart;

use JsonSerializable;
use Modules\Support\Money;
use Modules\Tax\Entities\TaxRate;
use Illuminate\Support\Collection;
use Modules\Coupon\Entities\Coupon;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductVariant;
use Modules\Shipping\Facades\ShippingMethod;
use Darryldecode\Cart\Cart as DarryldecodeCart;
use Modules\Variation\Entities\VariationValue;
use Modules\Product\Services\ChosenProductOptions;
use Modules\Product\Services\ChosenProductVariations;
use Darryldecode\Cart\Exceptions\InvalidItemException;
use Darryldecode\Cart\Exceptions\InvalidConditionException;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Modules\DeliveryCharge\Entities\ProductBoxWeightCharge;
use Modules\DeliveryCharge\Entities\ProductWeightCharge;
use Darryldecode\Cart\Helpers\Helpers;
use Darryldecode\Cart\ItemAttributeCollection;

class Cart extends DarryldecodeCart implements JsonSerializable
{
    public function instance(): static
    {
        return $this;
    }

    public function clear(): void
    {
        parent::clear();
        $this->clearCartConditions();
    }

    public function store($productId, $variantId, $qty, $options = []): void
    {
        $options = array_filter($options);
        $variations = [];

        $product = Product::with('files', 'categories', 'taxClass')->findOrFail($productId);
        $variant = ProductVariant::find($variantId);
        $item = $variant ?? $product;

        if ($variant) {
            $uids = collect(explode('.', $variant->uids));
            $rawVariations = $uids->map(function ($uid) {
                return VariationValue::where('uid', $uid)->get()->pluck('id', 'variation.id');
            });

            foreach ($rawVariations as $variation) {
                foreach ($variation as $variationId => $variationValueId) {
                    $variations[$variationId] = $variationValueId;
                }
            }
        }

        $chosenVariations = new ChosenProductVariations($product, $variations);
        $chosenOptions = new ChosenProductOptions($product, $options);

        $this->add([
            'id' => md5("product_id.{$productId}.variant_id.{$variantId}:options." . serialize($options)),
            'name' => $product->name,
            'weight' => $product->weight,
            'price' => $item->selling_price->amount(),
            'quantity' => (int)$qty,
            'attributes' => [
                'product' => $product,
                'variant' => $variant,
                'item' => $item,
                'variations' => $chosenVariations->getEntities(),
                'options' => $chosenOptions->getEntities(),
                'created_at' => time(),
            ],
        ]);
    }

    public function updateQuantity($id, $qty)
    {
        $this->update($id, [
            'quantity' => [
                'relative' => false,
                'value' => $qty,
            ],
        ]);
    }

    public function addedQty(CartItem $cartItem): int
    {
        $items = $this->items()->filter(function ($cartItemAlias) use ($cartItem) {
            if ($cartItem->variant && $cartItemAlias->variant) {
                return $cartItemAlias->variant->id === $cartItem->variant->id;
            }
            return $cartItemAlias->product->id === $cartItem->product->id;
        });
        return $items->sum('qty');
    }

    public function items()
    {
        return $this->getContent()
            ->sortBy('attributes.created_at', SORT_REGULAR, true)
            ->map(function ($item) {
                return new CartItem($item);
            });
    }

    public function crossSellProducts()
    {
        return $this->getAllProducts()
            ->load([
                'crossSellProducts' => function ($query) {
                    $query->forCard();
                },
            ])
            ->pluck('crossSellProducts')
            ->flatten();
    }

    public function getAllProducts(): EloquentCollection
    {
        return $this->items()->map(function ($cartItem) {
            return $cartItem->product;
        })->flatten()->pipe(function ($products) {
            return new EloquentCollection($products);
        });
    }

    public function reduceStock()
    {
        $this->manageStock(function ($cartItem) {
            $cartItem->item->decrement('qty', $cartItem->qty);
        });
    }

    public function restoreStock()
    {
        $this->manageStock(function ($cartItem) {
            $cartItem->product->increment('qty', $cartItem->qty);
        });
    }

    public function addShippingMethod($shippingMethod)
    {
        $this->removeShippingMethod();
        $this->condition(
            new CartCondition([
                'name' => $shippingMethod->label,
                'type' => 'shipping_method',
                'target' => 'total',
                'value' => $this->coupon()?->free_shipping ? 0 : $shippingMethod->cost->amount(),
                'order' => 1,
                'attributes' => [
                    'shipping_method' => $shippingMethod,
                ],
            ])
        );
        return $this->shippingMethod();
    }

    public function removeShippingMethod()
    {
        $this->removeConditionsByType('shipping_method');
    }

    public function coupon()
    {
        if (!$this->hasCoupon()) {
            return new NullCartCoupon();
        }
        $couponCondition = $this->getConditionsByType('coupon')->first();
        $coupon = Coupon::with('products', 'categories')->find($couponCondition->getAttribute('coupon_id'));
        return new CartCoupon($this, $coupon, $couponCondition);
    }

    public function hasCoupon()
    {
        if ($this->getConditionsByType('coupon')->isEmpty()) {
            return false;
        }
        $couponId = $this->getConditionsByType('coupon')
            ->first()
            ->getAttribute('coupon_id');
        return Coupon::where('id', $couponId)->exists();
    }

    private function getCouponEligibleSubtotal()
    {
        return $this->items()->reduce(function ($carry, $cartItem) {
            $rawItem = property_exists($cartItem, 'item') ? $cartItem->item : $cartItem;
            $product = $rawItem;
            if (!empty($product->special_price) && $product->special_price->amount() > 0) {
                return $carry; // Skip special-priced items
            }
            $price = $product->price instanceof Money ? $product->price->amount() : ($product->price ?? 0);
            $quantity = $rawItem->quantity ?? 1;
            $itemTotal = $price * $quantity;
            return $carry + $itemTotal;
        }, 0);
    }

    private function getCouponValue($coupon)
    {
        $eligibleSubtotal = $this->getCouponEligibleSubtotal();

        if ($coupon->is_percent) {
            $discount = $eligibleSubtotal * ($coupon->value / 100);
            return "-{$discount}";
        }

        $discount = min($coupon->value->amount(), $eligibleSubtotal);
        return "-{$discount}";
    }

    public function applyCoupon(Coupon $coupon)
    {
        $this->removeCoupon();

        try {
            $discountValue = $this->getCouponValue($coupon);
            if (!is_numeric(str_replace('-', '', $discountValue))) {
                throw new InvalidConditionException("Invalid discount value: {$discountValue}");
            }

            $condition = new CartCondition([
                'name' => $coupon->code,
                'type' => 'coupon',
                'target' => 'total',
                'value' => $discountValue,
                'order' => 2,
                'attributes' => [
                    'coupon_id' => $coupon->id,
                ],
            ]);

            $this->condition($condition);
        } catch (InvalidConditionException $e) {
            \Log::error('Failed to apply coupon: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            if (request()->ajax()) {
                return response()->json(['message' => trans('core::something_went_wrong')], 400);
            }
            throw $e;
        }

        if ($coupon->free_shipping) {
            // $this->addShippingMethod(ShippingMethod::get($this->shippingMethod()->name()));
        }
    }

    public function removeCoupon()
    {
        $this->removeConditionsByType('coupon');
    }

    public function shippingMethod()
    {
        if (!$this->hasShippingMethod()) {
            return new NullCartShippingMethod();
        }
        return new CartShippingMethod($this, $this->getConditionsByType('shipping_method')->first());
    }

    public function hasShippingMethod()
    {
        return $this->getConditionsByType('shipping_method')->isNotEmpty();
    }

    public function couponAlreadyApplied(Coupon $coupon)
    {
        return $this->coupon()->code() === $coupon->code;
    }

    public function discount()
    {
        return $this->coupon()->value();
    }

    public function addTaxes($addTaxesToCartRequest)
    {
        $this->removeTaxes();
        $this->findTaxes(
            $addTaxesToCartRequest->billing,
            $addTaxesToCartRequest->shipping
        )->each(function ($taxRate) {
            $this->condition(
                new CartCondition([
                    'name' => $taxRate->id,
                    'type' => 'tax',
                    'target' => 'total',
                    'value' => "+{$taxRate->rate}%",
                    'order' => 3,
                    'attributes' => [
                        'tax_rate_id' => $taxRate->id,
                    ],
                ])
            );
        });
    }

    public function removeTaxes()
    {
        $this->removeConditionsByType('tax');
    }

    public function __toString()
    {
        return json_encode($this->jsonSerialize());
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'items' => $this->items(),
            'quantity' => $this->getTotalQuantity(),
            'weight' => $this->getTotalWeight(),
            'availableShippingMethods' => $this->availableShippingMethods(),
            'subTotal' => $this->subTotal(),
            'shippingMethodName' => $this->shippingMethod()->name(),
            'shippingCost' => $this->shippingCarge(),
            'coupon' => $this->coupon(),
            'taxes' => $this->taxes(),
            'total' => $this->total(),
        ];
    }

    public function getWeightCharge()
    {
        $totalWeight = $this->getTotalWeight();
        $weightBasedCharge = ProductWeightCharge::getWeightCharge($totalWeight) ?? 0;
        $boxWeightCharge = ProductBoxWeightCharge::getBoxWeightCharge($totalWeight) ?? 0;
        return $weightBasedCharge + $boxWeightCharge;
    }

    public function shippingCarge()
    {
        if ($this->isFreeDeliveryCharge()) {
            return Money::inDefaultCurrency(0);
        }
        $coupon = $this->coupon();
        if ($coupon->free_shipping) {
            return Money::inDefaultCurrency(0);
        }
        $delivery_charge = getShippingCharge() ?? 0;
        $weightBasedCharge = $this->getWeightCharge();
        return Money::inDefaultCurrency($delivery_charge + $weightBasedCharge);
    }

    public function availableShippingMethods(): Collection
    {
        if ($this->allItemsAreVirtual()) {
            return collect();
        }
        return ShippingMethod::available();
    }

    public function allItemsAreVirtual()
    {
        return $this->items()->every(function (CartItem $cartItem) {
            return $cartItem->product->is_virtual;
        });
    }

    public function subTotal()
    {
        return Money::inDefaultCurrency($this->getSubTotal())->add($this->optionsPrice());
    }

    public function shippingCost()
    {
        return $this->shippingMethod()->cost();
    }

    public function taxes()
    {
        if (!$this->hasTax()) {
            return new Collection();
        }
        $taxConditions = $this->getConditionsByType('tax');
        $taxRates = TaxRate::whereIn('id', $this->getTaxRateIds($taxConditions))->get();
        return $taxConditions->map(function ($taxCondition) use ($taxRates) {
            $taxRate = $taxRates->where('id', $taxCondition->getAttribute('tax_rate_id'))->first();
            return new CartTax($this, $taxRate, $taxCondition);
        });
    }

    public function hasTax()
    {
        return $this->getConditionsByType('tax')->isNotEmpty();
    }

    public function total()
    {
        return $this->subTotal()
            ->add($this->shippingCarge())
            ->subtract($this->coupon()->value())
            ->add($this->tax());
    }

    public function tax()
    {
        return Money::inDefaultCurrency($this->calculateTax());
    }

    private function manageStock($callback)
    {
        $this->items()
            ->filter(function ($cartItem) {
                return $cartItem->item->manage_stock;
            })
            ->each($callback);
    }

    private function refreshFreeShippingCoupon()
    {
        if ($this->coupon()->isFreeShipping()) {
            $this->applyCoupon($this->coupon()->entity());
        }
    }

    private function findTaxes($billing_address, $shipping_address)
    {
        return $this->items()
            ->groupBy('tax_class_id')
            ->flatten()
            ->map(function (CartItem $cartItem) use ($billing_address, $shipping_address) {
                return $cartItem->findTax($billing_address, $shipping_address);
            })
            ->filter();
    }

    private function optionsPrice()
    {
        return Money::inDefaultCurrency($this->calculateOptionsPrice());
    }

    private function calculateOptionsPrice()
    {
        return $this->items()->sum(function ($cartItem) {
            return $cartItem
                ->optionsPrice()
                ->multiply($cartItem->qty)
                ->amount();
        });
    }

    private function getTaxRateIds($taxConditions)
    {
        return $taxConditions->map(function ($taxCondition) {
            return $taxCondition->getAttribute('tax_rate_id');
        });
    }

    private function calculateTax()
    {
        return $this->taxes()->sum(function ($cartTax) {
            return $cartTax->amount()->amount();
        });
    }

    public function add($id, $name = null, $price = null, $quantity = null, $attributes = array(), $conditions = array(), $associatedModel = null, $weight = null)
    {
        if (is_array($id)) {
            if (Helpers::isMultiArray($id)) {
                foreach ($id as $item) {
                    $this->add(
                        $item['id'],
                        $item['name'],
                        $item['price'],
                        $item['quantity'],
                        Helpers::issetAndHasValueOrAssignDefault($item['attributes'], array()),
                        Helpers::issetAndHasValueOrAssignDefault($item['conditions'], array()),
                        Helpers::issetAndHasValueOrAssignDefault($item['associatedModel'], null),
                        $item['weight'] ?? null
                    );
                }
            } else {
                $this->add(
                    $id['id'],
                    $id['name'],
                    $id['price'],
                    $id['quantity'],
                    Helpers::issetAndHasValueOrAssignDefault($id['attributes'], array()),
                    Helpers::issetAndHasValueOrAssignDefault($id['conditions'], array()),
                    Helpers::issetAndHasValueOrAssignDefault($id['associatedModel'], null),
                    $id['weight'] ?? null
                );
            }
            return $this;
        }

        $data = array(
            'id' => $id,
            'name' => $name,
            'weight' => $weight,
            'price' => Helpers::normalizePrice($price),
            'quantity' => $quantity,
            'attributes' => new ItemAttributeCollection($attributes),
            'conditions' => $conditions
        );

        if (isset($associatedModel) && $associatedModel != '') {
            $data['associatedModel'] = $associatedModel;
        }

        $item = $this->validate($data);

        $cart = $this->getContent();
        if ($cart->has($id)) {
            $this->update($id, $item);
        } else {
            $this->addRow($id, $item);
        }

        $this->currentItemId = $id;
        return $this;
    }

    public function getTotalWeight()
    {
        return $this->getContent()->sum(function ($item) {
            $weight = 0;
            if (isset($item->attributes['product'])) {
                $weight = $item->attributes['product']->weight ?? 0;
            }
            return $weight * $item->quantity;
        });
    }

    public function isFreeDeliveryCharge()
    {
        return ($this->subTotal()->amount() >= 1200) ? true : false;
    }
}
