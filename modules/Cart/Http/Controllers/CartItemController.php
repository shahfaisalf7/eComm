<?php

namespace Modules\Cart\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Cart\Facades\Cart;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\Controller;
use Modules\Coupon\Entities\Coupon;
use Modules\Coupon\Checkers\ValidCoupon;
use Modules\Coupon\Checkers\MaximumSpend;
use Modules\Coupon\Checkers\MinimumSpend;
use Modules\Coupon\Checkers\CouponExists;
use Modules\Coupon\Checkers\AlreadyApplied;
use Modules\Coupon\Checkers\ExcludedProducts;
use Modules\Coupon\Checkers\ApplicableProducts;
use Modules\Coupon\Checkers\ExcludedCategories;
use Modules\Coupon\Checkers\UsageLimitPerCoupon;
use Modules\Cart\Http\Middleware\CheckItemStock;
use Modules\Coupon\Checkers\ApplicableCategories;
use Modules\Coupon\Checkers\UsageLimitPerCustomer;
use Modules\Cart\Http\Requests\StoreCartItemRequest;

class CartItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(CheckItemStock::class)
            ->only(['store', 'update']);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCartItemRequest $request
     *
     * @return \Modules\Cart\Cart
     */
    public function store(StoreCartItemRequest $request)
    {
        Cart::store(
            $request->product_id,
            $request->variant_id,
            $request->qty,
            $request->options ?? [],
        );

        return Cart::instance();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param string $id
     *
     * @return \Modules\Cart\Cart
     */
    public function update(string $id)
    {
        Cart::updateQuantity($id, request('qty'));

        return Cart::instance();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     *
     * @return \Modules\Cart\Cart
     */
    public function destroy(string $id)
    {
        Cart::remove($id);

        return Cart::instance();
    }

    public function storeApi(StoreCartItemRequest $request)
    {
        Cart::store(
            $request->product_id,
            $request->variant_id,
            $request->qty,
            $request->options ?? [],
        );

        $data = Cart::instance();
        return responseWithData(__("Added Successfully"), $data);
    }

    public function getCartItems()
    {
        $data = Cart::instance();
        return responseWithData(__("Fetched Successfully"), $data);
    }

    public function updateApi(string $id)
    {
        Cart::updateQuantity($id, request('qty'));

        $data = Cart::instance();
        return responseWithData(__("Updated Successfully"), $data);
    }

    public function destroyApi(string $id)
    {
        Cart::remove($id);
        $data = Cart::instance();
        return responseWithData(__("Removed Successfully"), $data);
    }



    /**
     * Add multiple items to the cart via API (product_id and qty only).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Add multiple items to the cart via API (product_id and qty only).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Add multiple items to the cart via API (product_id and qty only).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Add multiple items to the cart via API (product_id and qty only).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMultipleApi()
    {
        $requestData = request()->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $outOfStock = [];
        $addedItems = [];
        $addedProducts = []; // Store product details for fallback

        foreach ($requestData['items'] as $index => $item) {
            try {
                // Check product existence and active status
                $product = \Modules\Product\Entities\Product::where('id', $item['product_id'])
                    ->where('is_active', 1)
                    ->first();

                if (!$product) {
                    \Log::warning("Invalid product_id {$item['product_id']} at index {$index}: Product not found or inactive");
                    continue;
                }

                // Check stock availability
                if ($product->isOutOfStock()) {
                    \Log::info("Product ID {$item['product_id']} at index {$index} is out of stock: {$product->name}");
                    $outOfStock[] = ($product->name ?? "Product ID {$item['product_id']}") . " is out of stock";
                    continue;
                }

                // Check flash sale stock
                if (\Modules\FlashSale\Entities\FlashSale::contains($product)) {
                    $remainingQty = \Modules\FlashSale\Entities\FlashSale::remainingQty($product);
                    if (!is_numeric($remainingQty) || $remainingQty < $item['qty']) {
                        \Log::info("Product ID {$item['product_id']} at index {$index} has insufficient flash sale stock: " .
                            ($remainingQty ?? 'null') . " remaining, {$item['qty']} requested");
                        $outOfStock[] = ($product->name ?? "Product ID {$item['product_id']}") . " is out of stock";
                        continue;
                    }
                } elseif ($product->manage_stock && $product->qty < $item['qty']) {
                    \Log::info("Product ID {$item['product_id']} at index {$index} has insufficient stock: " .
                        "{$product->qty} remaining, {$item['qty']} requested");
                    $outOfStock[] = ($product->name ?? "Product ID {$item['product_id']}") . " is out of stock";
                    continue;
                }

                // Add item to cart
                Cart::store(
                    $item['product_id'],
                    null, // No variant
                    $item['qty'],
                    [] // No options
                );

                $addedItems[] = $item['product_id'];
                $addedProducts[$item['product_id']] = [
                    'product' => $product,
                    'qty' => $item['qty']
                ];
                \Log::info("Added product ID {$item['product_id']} (qty: {$item['qty']}) to cart at index {$index}");
            } catch (\Exception $e) {
                \Log::error("Error processing item at index {$index} (product_id: {$item['product_id']}): {$e->getMessage()}");
                continue;
            }
        }

        $cart = Cart::instance();
        // Get cart items safely
        $rawCartItems = method_exists($cart, 'items') ? $cart->items() : (property_exists($cart, 'content') ? $cart->content : []);
        \Log::debug('Raw cart items: ' . json_encode($rawCartItems));

        // Filter cart items to include only added products
        $filteredItems = collect($rawCartItems)->filter(function ($cartItem) use ($addedItems) {
            try {
                if ($cartItem instanceof \Modules\Cart\CartItem) {
                    // Try accessing product ID through various properties
                    $product = $cartItem->product ?? null;
                    $productId = null;
                    if ($product && property_exists($product, 'id')) {
                        $productId = $product->id;
                    } elseif (property_exists($cartItem, 'product_id')) {
                        $productId = $cartItem->product_id;
                    }
                    \Log::debug("Cart item details: " . json_encode([
                            'product_id' => $productId,
                            'has_product' => !is_null($product),
                            'cart_item' => [
                                'id' => $cartItem->id ?? null,
                                'qty' => $cartItem->qty ?? null,
                                'product_exists' => property_exists($cartItem, 'product'),
                                'product_id_exists' => property_exists($cartItem, 'product_id')
                            ],
                            'addedItems' => $addedItems
                        ]));
                    return $productId && in_array($productId, $addedItems);
                }
                return false;
            } catch (\Exception $e) {
                \Log::error("Error filtering cart item: {$e->getMessage()}");
                return false;
            }
        })->map(function ($cartItem) use ($addedProducts) {
            try {
                // Convert CartItem to array for response
                $product = $cartItem->product ?? null;
                $productId = $product && property_exists($product, 'id') ? $product->id : (property_exists($cartItem, 'product_id') ? $cartItem->product_id : null);
                // Fallback to addedProducts if product is missing
                $product = $product ?? ($addedProducts[$productId]['product'] ?? null);
                $productArray = $product ? $product->toArray() : [];
                $priceData = $product ? (method_exists($product->price, 'toArray') ? $product->price->toArray() : (array) $product->price) : [];
                $priceAmount = isset($priceData['amount']) ? (float) $priceData['amount'] : 0;
                $priceFormatted = $priceData['formatted'] ?? 'BDT 0.00';
                $qty = $cartItem->qty ?? ($addedProducts[$productId]['qty'] ?? 1);
                return [
                    'id' => $cartItem->id ?? uniqid(),
                    'qty' => $qty,
                    'product' => $productArray,
                    'variant' => null,
                    'item' => $productArray,
                    'variations' => [],
                    'options' => [],
                    'unitPrice' => [
                        'amount' => $cartItem->unitPrice->amount ?? $priceAmount,
                        'formatted' => $cartItem->unitPrice->formatted ?? $priceFormatted,
                        'currency' => 'BDT',
                        'inCurrentCurrency' => [
                            'amount' => $cartItem->unitPrice->amount ?? $priceAmount,
                            'formatted' => $cartItem->unitPrice->formatted ?? $priceFormatted,
                            'currency' => 'BDT'
                        ]
                    ],
                    'total' => [
                        'amount' => $cartItem->total->amount ?? ($priceAmount * $qty),
                        'formatted' => $cartItem->total->formatted ?? ('BDT ' . number_format($priceAmount * $qty, 2)),
                        'currency' => 'BDT',
                        'inCurrentCurrency' => [
                            'amount' => $cartItem->total->amount ?? ($priceAmount * $qty),
                            'formatted' => $cartItem->total->formatted ?? ('BDT ' . number_format($priceAmount * $qty, 2)),
                            'currency' => 'BDT'
                        ]
                    ]
                ];
            } catch (\Exception $e) {
                \Log::error("Error mapping cart item: {$e->getMessage()}");
                return [];
            }
        })->filter()->keyBy('id')->toArray();

        // Fallback: If filteredItems is empty but addedItems exists, reconstruct items
        if (empty($filteredItems) && !empty($addedItems)) {
            \Log::warning("No items found in cart, reconstructing from addedItems");
            $filteredItems = collect($addedItems)->map(function ($productId) use ($addedProducts) {
                $product = $addedProducts[$productId]['product'] ?? null;
                $qty = $addedProducts[$productId]['qty'] ?? 1;
                $productArray = $product ? $product->toArray() : [];
                $priceData = $product ? (method_exists($product->price, 'toArray') ? $product->price->toArray() : (array) $product->price) : [];
                $priceAmount = isset($priceData['amount']) ? (float) $priceData['amount'] : 0;
                $priceFormatted = $priceData['formatted'] ?? 'BDT 0.00';
                return [
                    'id' => uniqid(),
                    'qty' => $qty,
                    'product' => $productArray,
                    'variant' => null,
                    'item' => $productArray,
                    'variations' => [],
                    'options' => [],
                    'unitPrice' => [
                        'amount' => $priceAmount,
                        'formatted' => $priceFormatted,
                        'currency' => 'BDT',
                        'inCurrentCurrency' => [
                            'amount' => $priceAmount,
                            'formatted' => $priceFormatted,
                            'currency' => 'BDT'
                        ]
                    ],
                    'total' => [
                        'amount' => $priceAmount * $qty,
                        'formatted' => 'BDT ' . number_format($priceAmount * $qty, 2),
                        'currency' => 'BDT',
                        'inCurrentCurrency' => [
                            'amount' => $priceAmount * $qty,
                            'formatted' => 'BDT ' . number_format($priceAmount * $qty, 2),
                            'currency' => 'BDT'
                        ]
                    ]
                ];
            })->keyBy('id')->toArray();
        }

        \Log::debug('Filtered items: ' . json_encode($filteredItems));

        // Construct cart response
        $cartData = [
            'items' => $filteredItems,
            'quantity' => count($filteredItems),
            'weight' => collect($filteredItems)->sum(function ($item) {
                return isset($item['product']['weight']) ? (float) $item['product']['weight'] : 0;
            }),
            'subTotal' => [
                'amount' => collect($filteredItems)->sum(function ($item) {
                    return isset($item['total']['amount']) ? (float) $item['total']['amount'] : 0;
                }),
                'formatted' => 'BDT ' . number_format(collect($filteredItems)->sum(function ($item) {
                        return isset($item['total']['amount']) ? (float) $item['total']['amount'] : 0;
                    }), 2),
                'currency' => 'BDT',
                'inCurrentCurrency' => [
                    'amount' => collect($filteredItems)->sum(function ($item) {
                        return isset($item['total']['amount']) ? (float) $item['total']['amount'] : 0;
                    }),
                    'formatted' => 'BDT ' . number_format(collect($filteredItems)->sum(function ($item) {
                            return isset($item['total']['amount']) ? (float) $item['total']['amount'] : 0;
                        }), 2),
                    'currency' => 'BDT'
                ]
            ],
            'availableShippingMethods' => [
                'flat_rate' => [
                    'name' => 'flat_rate',
                    'label' => 'Flat Rate',
                    'cost' => [
                        'amount' => '120',
                        'formatted' => 'BDT 120.00',
                        'currency' => 'BDT'
                    ]
                ]
            ],
            'shippingMethodName' => null,
            'shippingCost' => [
                'amount' => 0,
                'formatted' => 'BDT 0.00',
                'currency' => 'BDT'
            ],
            'coupon' => [],
            'taxes' => [],
            'total' => [
                'amount' => collect($filteredItems)->sum(function ($item) {
                    return isset($item['total']['amount']) ? (float) $item['total']['amount'] : 0;
                }),
                'formatted' => 'BDT ' . number_format(collect($filteredItems)->sum(function ($item) {
                        return isset($item['total']['amount']) ? (float) $item['total']['amount'] : 0;
                    }), 2),
                'currency' => 'BDT',
                'inCurrentCurrency' => [
                    'amount' => collect($filteredItems)->sum(function ($item) {
                        return isset($item['total']['amount']) ? (float) $item['total']['amount'] : 0;
                    }),
                    'formatted' => 'BDT ' . number_format(collect($filteredItems)->sum(function ($item) {
                            return isset($item['total']['amount']) ? (float) $item['total']['amount'] : 0;
                        }), 2),
                    'currency' => 'BDT'
                ]
            ]
        ];

        $response = [
            'status' => empty($filteredItems) && !empty($outOfStock) ? 'error' : 'success',
            'message' => empty($filteredItems) && !empty($outOfStock)
                ? __("No items could be added to the cart")
                : __("Multiple Items Added Successfully"),
            'data' => $cartData
        ];

        if (!empty($outOfStock)) {
            $response['out_of_stock'] = $outOfStock;
        }

        return response()->json($response);
    }






}
