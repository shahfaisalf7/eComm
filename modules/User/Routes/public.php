<?php

use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::get('login', 'AuthController@getLogin')->name('login');
Route::post('login', 'AuthController@postLogin')->name('login.post');

// OTP login
Route::post('request-otp', 'AuthController@requestOtp')->name('request-otp');
Route::post('verify-otp', 'AuthController@verifyOtp')->name('verify-otp');
Route::post('user-login', 'AuthController@userLogin')->name('user-login');

Route::get('login/{provider}', 'AuthController@redirectToProvider')->name('login.redirect');
Route::get('login/{provider}/callback', 'AuthController@handleProviderCallback')->name('login.callback');

Route::get('logout', 'AuthController@getLogout')->name('logout');

Route::get('register', 'AuthController@getRegister')->name('register');
Route::post('register', 'AuthController@postRegister')
    ->name('register.post')
    ->middleware(ProtectAgainstSpam::class);

Route::get('password/reset', 'AuthController@getReset')->name('reset');
Route::post('password/reset', 'AuthController@postReset')->name('reset.post');
Route::get('password/reset/{email}/{code}', 'AuthController@getResetComplete')->name('reset.complete');
Route::post('password/reset/{email}/{code}', 'AuthController@postResetComplete')->name('reset.complete.post');


Route::prefix('api/v1')->group(function () {
    Route::post('login', 'AuthController@postLogin')->name('api.login');
    Route::post('logout', 'AuthController@postLogout');

    Route::post('request-otp', 'AuthController@requestOtp');
    Route::post('verify-otp', 'AuthController@verifyOtp');
    Route::post('user-login', 'AuthController@userLogin');

    Route::get('login/{provider}', 'AuthController@redirectToProvider');
    Route::get('login/{provider}/callback', 'AuthController@handleProviderCallback');

    Route::get('logout', 'AuthController@getLogout');

    Route::get('register', 'AuthController@getRegister');
    Route::post('register', 'AuthController@postRegister')->middleware(ProtectAgainstSpam::class);

    Route::get('password/reset', 'AuthController@getReset');
    Route::post('password/reset', 'AuthController@postReset');
    Route::get('password/reset/{email}/{code}', 'AuthController@getResetComplete');
    Route::post('password/reset/{email}/{code}', 'AuthController@postResetComplete');

    Route::post('send-sms', 'AuthController@sendSms')->name('send-sms');
});
