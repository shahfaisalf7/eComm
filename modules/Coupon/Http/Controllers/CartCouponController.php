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

    public function storeApi()
    {
        $coupon = Coupon::findByCode(request('coupon'));

        try {
            resolve(Pipeline::class)
                ->send($coupon)
                ->through($this->checkers)
                ->then(function ($coupon) {
                    Cart::applyCoupon($coupon);
                });
        } catch (\Modules\Coupon\Exceptions\CouponAlreadyAppliedException $e) {
            return responseInvalidRequest(trans('coupon::messages.already_applied'));
        } catch (\Modules\Coupon\Exceptions\CouponNotExistsException $e) {
            return responseInvalidRequest(trans('coupon::messages.not_exists'));
        } catch (\Modules\Coupon\Exceptions\CouponUsageLimitReachedException $e) {
            return responseInvalidRequest(trans('coupon::messages.usage_limit_reached'));
        } catch (\Modules\Coupon\Exceptions\CouponSpecialPriceExclusionException $e) {
            return responseInvalidRequest($e->getMessage());
        } catch (\Exception $e) {
            return responseWithError($e->getMessage());
        }

        $data = Cart::instance();
        return responseWithData(trans('coupon::messages.added_successfully'), $data);
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
