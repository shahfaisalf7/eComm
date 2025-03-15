<?php

use Illuminate\Support\Facades\Route;

Route::post('cart/coupon', 'CartCouponController@store')->name('cart.coupon.store');
Route::delete('cart/coupon', 'CartCouponController@destroy')->name('cart.coupon.destroy');

Route::prefix('api/v1')->group(function () {
    Route::post('cart/coupon', 'CartCouponController@storeApi');
    Route::delete('cart/coupon', 'CartCouponController@destroyApi');
});
