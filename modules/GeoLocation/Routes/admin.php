<?php

use Illuminate\Support\Facades\Route;
use Modules\GeoLocation\Http\Controllers\GeoLocationController;

Route::middleware(['admin'])->group(function () {
    Route::get('/geo-division-sidebar', [GeoLocationController::class, 'indexDivision'])->name('admin.geo.division.sidebar');
    Route::get('/geo-division-table', [GeoLocationController::class, 'tableDivision'])->name('admin.geo.division.table');
    Route::get('/geo-division-edit/{id}', [GeoLocationController::class, 'editDivision'])->name('admin.geo.division.edit');
    Route::get('/geo-division-create', [GeoLocationController::class, 'createDivision'])->name('admin.geo.division.create');
    Route::post('/geo-division-store', [GeoLocationController::class, 'storeDivision'])->name('admin.geo.division.store');
    Route::put('/geo-division-update/{id}', [GeoLocationController::class, 'updateDivision'])->name('admin.geo.division.update');
    Route::delete('/geo-division-destroy', [GeoLocationController::class, 'destroyDivision'])->name('admin.geo.division.destroy');

    Route::get('/geo-cities-sidebar', [GeoLocationController::class, 'indexCities'])->name('admin.geo.cities.sidebar');
    Route::get('/geo-cities-table', [GeoLocationController::class, 'tableCities'])->name('admin.geo.cities.table');
    Route::get('/geo-cities-edit/{id}', [GeoLocationController::class, 'editCities'])->name('admin.geo.cities.edit');
    Route::get('/geo-cities-create', [GeoLocationController::class, 'createCities'])->name('admin.geo.cities.create');
    Route::post('/geo-cities-store', [GeoLocationController::class, 'storeCities'])->name('admin.geo.cities.store');
    Route::put('/geo-cities-update/{id}', [GeoLocationController::class, 'updateCities'])->name('admin.geo.cities.update');
    Route::delete('/geo-cities-destroy', [GeoLocationController::class, 'destroyCities'])->name('admin.geo.cities.destroy');

    Route::get('/geo-cities-byDivision', [GeoLocationController::class, 'citiesbyDivision'])->name('admin.geo.cities.byDivision');

    Route::get('/geo-zones-sidebar', [GeoLocationController::class, 'indexZones'])->name('admin.geo.zones.sidebar');
    Route::get('/geo-zones-table', [GeoLocationController::class, 'tableZones'])->name('admin.geo.zones.table');
    Route::get('/geo-zones-edit/{id}', [GeoLocationController::class, 'editZones'])->name('admin.geo.zones.edit');
    Route::get('/geo-zones-create', [GeoLocationController::class, 'createZones'])->name('admin.geo.zones.create');
    Route::post('/geo-zones-store', [GeoLocationController::class, 'storeZones'])->name('admin.geo.zones.store');
    Route::put('/geo-zones-update/{id}', [GeoLocationController::class, 'updateZones'])->name('admin.geo.zones.update');
    Route::delete('/geo-zones-destroy', [GeoLocationController::class, 'destroyZones'])->name('admin.geo.zones.destroy');
});

Route::prefix('api/v1')->group(function () {
    Route::middleware('auth.api')->group(function () {
        Route::get('get/geo-division', [GeoLocationController::class, 'getGeoDivision']);
    });
});
