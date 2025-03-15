<?php

use Illuminate\Support\Facades\Route;

Route::get('checkout', 'CheckoutController@create')->name('checkout.create');
Route::post('checkout', 'CheckoutController@store')->name('checkout.store');

Route::any('checkout/{orderId}/complete', 'CheckoutCompleteController@store')
    ->name('checkout.complete.store')
    ->withoutMiddleware(\FleetCart\Http\Middleware\VerifyCsrfToken::class);
Route::get('checkout/complete', 'CheckoutCompleteController@show')->name('checkout.complete.show');

Route::get('checkout/{orderId}/payment-canceled', 'PaymentCanceledController@store')->name('checkout.payment_canceled.store');

Route::prefix('api/v1')->group(function () {
    Route::middleware('auth.api')->group(function () {
        Route::get('checkout/api', 'CheckoutController@createApi');
        Route::post('checkout/api', 'CheckoutController@storeApi');
    });
});
