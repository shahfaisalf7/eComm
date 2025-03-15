<?php

namespace Modules\Checkout\Services;

use Modules\Cart\CartTax;
use Modules\Cart\CartItem;
use Modules\Cart\Facades\Cart;
use Modules\Order\Entities\Order;
use Modules\Address\Entities\Address;
use Modules\FlashSale\Entities\FlashSale;
use Modules\Currency\Entities\CurrencyRate;
use Modules\Account\Entities\DefaultAddress;
use Modules\Coupon\Entities\Coupon;
use Modules\DeliveryCharge\Entities\Zone;
use Modules\Shipping\Facades\ShippingMethod;

class OrderService
{
    public function create($request)
    {
        $this->mergeShippingAddress($request);
        $this->saveAddress($request);
        // $this->addShippingMethodToCart($request);

        return tap($this->store($request), function ($order) {
            $this->storeOrderProducts($order);
            $this->storeOrderDownloads($order);
            $this->storeFlashSaleProductOrders($order);
            $this->incrementCouponUsage();
            $this->attachTaxes($order);
            $this->reduceStock();
        });
    }


    public function reduceStock()
    {
        Cart::reduceStock();
    }


    public function delete(Order $order)
    {
        $order->delete();

        Cart::restoreStock();
    }


    private function mergeShippingAddress($request)
    {
        $request->merge([
            'shipping' => $request->ship_to_a_different_address ? $request->shipping : $request->billing,
        ]);
    }


    private function saveAddress($request)
    {
        if (auth()->guest()) {
            return;
        }

        if ($request->newBillingAddress) {
            $billing_data = $this->prepareAddressData($request->billing);
            $address = auth()
                ->user()
                ->addresses()
                ->create($this->extractAddress($billing_data));

            $this->makeDefaultAddress($address);
        }

        if ($request->ship_to_a_different_address && $request->newShippingAddress) {
            $shipping_data = $this->prepareAddressData($request->shipping);
            auth()
                ->user()
                ->addresses()
                ->create($this->extractAddress($shipping_data));
        }
    }

    private function prepareAddressData($request_data)
    {
        $m_zone = new Zone();
        $zone = $m_zone->getZone($request_data['zone']);

        $data['id'] = $request_data['id'] ?? null;
        $data['name'] = $request_data['full_name'] ?? auth()->user()->full_name;
        // $data['first_name'] = $request_data['first_name'] ?? auth()->user()->first_name;
        // $data['last_name'] = $request_data['last_name'] ?? auth()->user()->last_name;
        $data['address_1'] = $request_data['address_1'] ?? '';
        $data['address_2'] = $request_data['address_2'] ?? '';
        $data['country'] = $request_data['country'] ?? "BD";
        $data['zone_id'] = $request_data['zone'];
        $data['zone'] = $zone->name;
        $data['city'] = $zone->city->name;
        $data['state'] = $zone->city->division->name;
        $data['zip'] = '';
        return $data;
    }


    private function extractAddress($data)
    {
        return [
            'name' => $data['name'],
            // 'first_name' => $data['first_name'],
            // 'last_name' => $data['last_name'],
            'address_1' => $data['address_1'],
            'address_2' => $data['address_2'] ?? null,
            'city' => $data['city'],
            'state' => $data['state'],
            'zone' => $data['zone'],
            'zone_id' => $data['zone_id'],
            'zip' => $data['zip'],
            'country' => $data['country'] ?? "BD",
        ];
    }


    private function makeDefaultAddress(Address $address)
    {
        if (
            auth()
            ->user()
            ->addresses()
            ->count() > 1
        ) {
            return;
        }

        DefaultAddress::create([
            'address_id' => $address->id,
            'customer_id' => auth()->id(),
        ]);
    }


    private function addShippingMethodToCart($request)
    {
        if (!Cart::allItemsAreVirtual() && !Cart::hasShippingMethod()) {
            Cart::addShippingMethod(ShippingMethod::get($request->shipping_method));
        }
    }


    private function store($request)
    {
        if (intval($request->billing['zone']) > 0) {
            $billing_data = $this->prepareAddressData($request->billing);
            $billing_data['full_name'] = $billing_data['name'];
            $request->merge(['billing' => $billing_data]);
        }
        if (intval($request->shipping['zone']) > 0) {
            $shipping_data = $this->prepareAddressData($request->shipping);
            $shipping_data['full_name'] = $shipping_data['name'];
            $request->merge(['shipping' => $shipping_data]);
        }
        $order_total = Cart::total()->amount();
        $order_shipping_cost = Cart::shippingCarge()->amount();
        if (Cart::coupon()->id() > 0) {
            $coupon_id = Cart::coupon()->id();
            $coupon = Coupon::find($coupon_id);
            if (!empty($coupon) && $coupon->free_shipping == 1) {
                $sub_total =  Cart::subTotal()->amount();
                $discount = Cart::discount()->amount();
                $order_total = $sub_total - $discount;
                $order_shipping_cost = 0;
            }
        }
        return Order::create([
            'customer_id' => auth()->id(),
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'customer_full_name' => $request->billing['full_name'],
            'billing_full_name' => $request->billing['full_name'],
            // 'customer_first_name' => $request->billing['first_name'],
            // 'customer_last_name' => $request->billing['last_name'],
            // 'billing_first_name' => $request->billing['first_name'],
            // 'billing_last_name' => $request->billing['last_name'],
            'billing_address_1' => $request->billing['address_1'],
            'billing_address_2' => $request->billing['address_2'] ?? null,
            'billing_city' => $request->billing['city'],
            'billing_state' => $request->billing['state'],
            'billing_zone' => $request->billing['zone'],
            'billing_zip' => $request->billing['zip'] ?? null,
            'billing_country' => $request->billing['country'] ?? "BD",
            'shipping_full_name' => $request->shipping['full_name'],
            // 'shipping_first_name' => $request->shipping['first_name'],
            // 'shipping_last_name' => $request->shipping['last_name'],
            'shipping_address_1' => $request->shipping['address_1'],
            'shipping_address_2' => $request->shipping['address_2'] ?? null,
            'shipping_city' => $request->shipping['city'],
            'shipping_state' => $request->shipping['state'],
            'shipping_zone' => $request->shipping['zone'],
            'shipping_zip' => $request->shipping['zip'] ?? null,
            'shipping_country' => $request->shipping['country'] ?? "BD",
            'sub_total' => Cart::subTotal()->amount(),
            'shipping_method' => Cart::shippingMethod()->name(),
            // 'shipping_cost' => getShippingCharge(),
            'shipping_cost' => $order_shipping_cost,
            'coupon_id' => Cart::coupon()->id(),
            'discount' => Cart::discount()->amount(),
            'total' => $order_total,
            'payment_method' => $request->payment_method,
            'currency' => currency(),
            'currency_rate' => CurrencyRate::for(currency()),
            'locale' => locale(),
            'status' => Order::PENDING_PAYMENT,
            'note' => $request->order_note,
        ]);
    }


    private function storeOrderProducts(Order $order)
    {
        Cart::items()->each(function (CartItem $cartItem) use ($order) {
            $order->storeProducts($cartItem);
        });
    }


    private function storeOrderDownloads(Order $order)
    {
        Cart::items()->each(function (CartItem $cartItem) use ($order) {
            $order->storeDownloads($cartItem);
        });
    }


    private function storeFlashSaleProductOrders(Order $order)
    {
        Cart::items()->each(function (CartItem $cartItem) use ($order) {
            if (!FlashSale::contains($cartItem->product)) {
                return;
            }

            FlashSale::pivot($cartItem->product)
                ->orders()
                ->attach([
                    $cartItem->product->id => [
                        'order_id' => $order->id,
                        'qty' => $cartItem->qty,
                    ],
                ]);
        });
    }


    private function incrementCouponUsage()
    {
        Cart::coupon()->usedOnce();
    }


    private function attachTaxes(Order $order)
    {
        Cart::taxes()->each(function (CartTax $cartTax) use ($order) {
            $order->attachTax($cartTax);
        });
    }
}
