<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('request/otp-code', 'AccountProfileController@requestOtpCode')->name('request-otp-code');
    Route::post('account/email-update', 'AccountProfileController@updateEmail')->name('account-email-update');
    Route::post('account/phone-update', 'AccountProfileController@updatePhone')->name('account-phone-update');
    Route::post('account/password-update', 'AccountProfileController@updatePassword')->name('account-password-update');

    Route::get('account', 'AccountDashboardController@index')->name('account.dashboard.index');

    Route::get('account/profile', 'AccountProfileController@edit')->name('account.profile.edit');
    Route::put('account/profile', 'AccountProfileController@update')->name('account.profile.update');

    Route::get('account/orders', 'AccountOrdersController@index')->name('account.orders.index');
    Route::get('account/orders/{id}', 'AccountOrdersController@show')->name('account.orders.show');

    Route::get('account/downloads', 'AccountDownloadsController@index')->name('account.downloads.index');
    Route::get('account/downloads/{id}', 'AccountDownloadsController@show')->name('account.downloads.show');

    Route::get('account/wishlist', 'AccountWishlistController@index')->name('account.wishlist.index');

    Route::get('account/wishlist/products', 'AccountWishlistProductController@index')->name('account.wishlist.products.index');
    Route::post('account/wishlist/products', 'AccountWishlistProductController@store')->name('account.wishlist.products.store');
    Route::delete('account/wishlist/products/{product}', 'AccountWishlistProductController@destroy')->name('account.wishlist.products.destroy');

    Route::get('account/reviews', 'AccountReviewController@index')->name('account.reviews.index');

    Route::get('account/addresses', 'AccountAddressController@index')->name('account.addresses.index');
    Route::post('account/addresses', 'AccountAddressController@store')->name('account.addresses.store');
    Route::put('account/addresses/{id}', 'AccountAddressController@update')->name('account.addresses.update');
    Route::delete('account/addresses/{id}', 'AccountAddressController@destroy')->name('account.addresses.destroy');
    Route::post('account/addresses/change-default', 'AccountAddressController@changeDefault')->name('account.addresses.change_default');
});




Route::prefix('api/v1')->group(function () {

    Route::middleware('auth.api')->group(function () {
        Route::post('request/otp-code', 'AccountProfileController@requestOtpCode');
        Route::post('account/email-update', 'AccountProfileController@updateEmail');
        Route::post('account/phone-update', 'AccountProfileController@updatePhone');
        Route::post('account/password-update', 'AccountProfileController@updatePassword');


        Route::get('account', 'AccountDashboardController@apiIndex');
        Route::get('account/profile', 'AccountProfileController@apiEdit');
        Route::put('account/profile', 'AccountProfileController@update');

        Route::get('account/orders', 'AccountOrdersController@apiIndex');
        Route::get('account/orders/{id}', 'AccountOrdersController@apiShow');

        Route::get('account/downloads', 'AccountDownloadsController@apiIndex');
        Route::get('account/downloads/{id}', 'AccountDownloadsController@show');

        Route::get('account/wishlist', 'AccountWishlistController@index');

        Route::get('account/wishlist/products', 'AccountWishlistProductController@indexApi');
        Route::post('account/wishlist/products', 'AccountWishlistProductController@storeApi');
        Route::delete('account/wishlist/products/{product}', 'AccountWishlistProductController@destroy');

        Route::get('account/reviews', 'AccountReviewController@index');

        Route::get('account/addresses', 'AccountAddressController@apiIndex');
        Route::post('account/addresses', 'AccountAddressController@storeApi');
        Route::put('account/addresses/{id}', 'AccountAddressController@updateApi');
        Route::delete('account/addresses/{id}', 'AccountAddressController@destroyApi');
        Route::post('account/addresses/change-default', 'AccountAddressController@changeDefaultApi');
    });
});
