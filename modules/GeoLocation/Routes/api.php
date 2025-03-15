<?php

use Illuminate\Support\Facades\Route;
use Modules\GeoLocation\Http\Controllers\GeoLocationController;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::prefix('v1')->group(function () {
    Route::middleware('auth.api')->group(function () {
        Route::get('geo/division', [GeoLocationController::class, 'getGeoDivision']);
        Route::get('geo/cities', [GeoLocationController::class, 'getGeoCities']);
        Route::get('geo/zones', [GeoLocationController::class, 'getGeoZones']);
    });
});
