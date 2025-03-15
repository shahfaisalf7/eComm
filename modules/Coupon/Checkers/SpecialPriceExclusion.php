<?php
namespace Modules\Coupon\Checkers;

use Modules\Cart\Facades\Cart;
use Modules\Coupon\Entities\Coupon;
use Modules\Coupon\Exceptions\CouponSpecialPriceExclusionException;

class SpecialPriceExclusion
{
    public function handle(Coupon $coupon, $next)
    {
        $items = Cart::items();

        if ($items->isEmpty()) {
            return $next($coupon);
        }

        $allHaveSpecialPrice = $items->every(function ($cartItem) {
            $product = $cartItem->product;
            return !empty($product->special_price) && $product->selling_price->amount() < $product->price->amount();
        });

        if ($allHaveSpecialPrice) {
            throw new CouponSpecialPriceExclusionException(
                trans('coupon::messages.coupon_cannot_be_applied_to_offer_price_products')
            );
        }

        return $next($coupon);
    }
}
