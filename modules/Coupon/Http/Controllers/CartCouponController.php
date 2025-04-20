<?php
namespace Modules\Coupon\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Cart\Facades\Cart;
use Illuminate\Pipeline\Pipeline;
use Modules\Coupon\Entities\Coupon;
use Modules\Coupon\Checkers\ValidCoupon;
use Modules\Coupon\Checkers\CouponExists;
use Modules\Coupon\Checkers\MaximumSpend;
use Modules\Coupon\Checkers\MinimumSpend;
use Modules\Coupon\Checkers\AlreadyApplied;
use Modules\Coupon\Checkers\ExcludedProducts;
use Modules\Coupon\Checkers\ApplicableProducts;
use Modules\Coupon\Checkers\ExcludedCategories;
use Modules\Coupon\Checkers\UsageLimitPerCoupon;
use Modules\Coupon\Checkers\ApplicableCategories;
use Modules\Coupon\Checkers\UsageLimitPerCustomer;
use Modules\Coupon\Checkers\SpecialPriceExclusion;

class CartCouponController
{
    private array $checkers = [
        CouponExists::class,
        AlreadyApplied::class,
        ValidCoupon::class,
        MinimumSpend::class,
        MaximumSpend::class,
        ApplicableProducts::class,
        ExcludedProducts::class,
        ApplicableCategories::class,
        ExcludedCategories::class,
        UsageLimitPerCoupon::class,
        UsageLimitPerCustomer::class,
        SpecialPriceExclusion::class,
    ];

    public function store(Request $request)
    {
        try {
            $coupon = Coupon::findByCode($request->input('coupon'));

            app(Pipeline::class)
                ->send($coupon)
                ->through($this->checkers)
                ->then(function ($coupon) {
                    Cart::applyCoupon($coupon);
                });

            return Cart::instance();
        } catch (\Throwable $e) { // Catch all errors and exceptions
            \Log::error('Coupon application failed: ' . $e->getMessage(), [
                'exception' => $e,
                'coupon' => $request->input('coupon'),
                'cart' => Cart::instance()->toArray(),
            ]);

            // Force translation or fallback
            $message = trans($e->getMessage()) === $e->getMessage()
                ? 'Coupon cannot be applied to offer price products'
                : trans($e->getMessage());

            return response()->json(['message' => $message], 422);
        }
    }

//    public function storeApi()
//    {
//        $coupon = Coupon::findByCode(request('coupon'));
//
//        try {
//            resolve(Pipeline::class)
//                ->send($coupon)
//                ->through($this->checkers)
//                ->then(function ($coupon) {
//                    Cart::applyCoupon($coupon);
//                });
//        } catch (\Modules\Coupon\Exceptions\CouponAlreadyAppliedException $e) {
//            return responseInvalidRequest(trans('coupon::messages.already_applied'));
//        } catch (\Modules\Coupon\Exceptions\CouponNotExistsException $e) {
//            return responseInvalidRequest(trans('coupon::messages.not_exists'));
//        } catch (\Modules\Coupon\Exceptions\CouponUsageLimitReachedException $e) {
//            return responseInvalidRequest(trans('coupon::messages.usage_limit_reached'));
//        } catch (\Modules\Coupon\Exceptions\CouponSpecialPriceExclusionException $e) {
//            return responseInvalidRequest($e->getMessage());
//        } catch (\Exception $e) {
//            return responseWithError($e->getMessage());
//        }
//
//        $data = Cart::instance();
//        return responseWithData(trans('coupon::messages.added_successfully'), $data);
//    }


    public function storeApi(Request $request)
    {
        try {
            $coupon = Coupon::findByCode($request->input('coupon'));
            if (!$coupon) {
                throw new \Exception('Coupon not found');
            }

            // Pre-pipeline cart analysis
            $cart = Cart::instance();
            $cartArray = $cart->toArray();
            $items = collect($cartArray['items'] ?? []);

            \Log::info('Raw Cart Items', ['items' => $items->toArray()]);

            $hasDiscountedItems = $items->isNotEmpty() && $items->some(function ($item) {
                    $specialPrice = $item->product->special_price;
                    \Log::info('Special Price Raw', [
                        'product_id' => $item->product->id,
                        'special_price' => json_encode($specialPrice),
                    ]);

                    $specialPriceAmount = null;
                    if ($specialPrice) {
                        $specialPriceArray = $specialPrice->toArray();
                        $specialPriceAmount = $specialPriceArray['amount'] ?? null;
                    }

                    $isDiscounted = $specialPriceAmount !== null && (float)$specialPriceAmount > 0;
                    \Log::info('Item Check', [
                        'product_id' => $item->product->id,
                        'special_price' => $specialPriceAmount,
                        'is_discounted' => $isDiscounted,
                    ]);
                    return $isDiscounted;
                });

            \Log::info('Cart Analysis', [
                'item_count' => $items->count(),
                'has_discounted_items' => $hasDiscountedItems,
            ]);

            // Apply coupon through pipeline
            app(Pipeline::class)
                ->send($coupon)
                ->through($this->checkers)
                ->then(function ($coupon) {
                    Cart::applyCoupon($coupon);
                });

            $message = $items->isEmpty()
                ? trans('coupon::messages.added_successfully', ['default' => 'Added successfully'])
                : ($hasDiscountedItems
                    ? trans('coupon::messages.added_successfully_partial', ['default' => 'Added successfully, applied only to non-offer price products'])
                    : trans('coupon::messages.added_successfully', ['default' => 'Added successfully']));

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $message,
                'data' => $cart,
            ], 200);
        } catch (\Throwable $e) {
            \Log::error('Coupon application failed: ' . $e->getMessage(), [
                'exception' => $e,
                'coupon' => $request->input('coupon'),
                'cart' => Cart::instance()->toArray(),
            ]);

            $message = $e->getMessage() === 'Coupon not found'
                ? 'Coupon not found'
                : ($e instanceof \Modules\Coupon\Exceptions\CouponSpecialPriceExclusionException
                    ? 'Coupon cannot be applied to offer price products'
                    : 'Coupon cannot be applied due to cart restrictions');

            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => $message,
            ], 422);
        }
    }


    public function destroy(): \Modules\Cart\Cart
    {
        Cart::removeCoupon();
        return Cart::instance();
    }

    public function destroyApi()
    {
        Cart::removeCoupon();
        $data = Cart::instance();
        return responseWithData(trans('coupon::messages.removed_successfully'), $data);
    }
}
