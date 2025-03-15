<?php

use Illuminate\Support\Facades\Route;
use Modules\DeliveryCharge\Http\Controllers\DeliveryChargeController;

Route::middleware(['admin'])->group(function () {
    // Delivery
    Route::get('/delivery-charge', [DeliveryChargeController::class, 'indexDelivery'])->name('admin.delivery.charge');
    Route::get('/delivery-charge-table', [DeliveryChargeController::class, 'showDelivery'])->name('admin.delivery.charge.table');
    Route::get('/delivery-charge-edit/{id}', [DeliveryChargeController::class, 'editDelivery'])->name('admin.delivery.charge.edit');
    Route::get('/delivery-charge-create', [DeliveryChargeController::class, 'createDelivery'])->name('admin.delivery.charge.create');
    Route::post('/delivery-charge-store', [DeliveryChargeController::class, 'storeDelivery'])->name('admin.delivery.charge.store');
    Route::put('/delivery-charge-update/{id}', [DeliveryChargeController::class, 'updateDelivery'])->name('admin.delivery.charge.update');
    Route::delete('/delivery-charge-destroy', [DeliveryChargeController::class, 'destroyDelivery'])->name('admin.delivery.charge.destroy');
    // Box
    Route::get('/product-charge', [DeliveryChargeController::class, 'indexProduct'])->name('admin.product.charge');
    Route::get('/product-charge-table', [DeliveryChargeController::class, 'showProduct'])->name('admin.product.charge.table');
    Route::get('/product-charge-edit/{id}', [DeliveryChargeController::class, 'editProduct'])->name('admin.product.charge.edit');
    Route::get('/product-charge-create', [DeliveryChargeController::class, 'createProduct'])->name('admin.product.charge.create');
    Route::post('/product-charge-store', [DeliveryChargeController::class, 'storeProduct'])->name('admin.product.charge.store');
    Route::put('/product-charge-update/{id}', [DeliveryChargeController::class, 'updateProduct'])->name('admin.product.charge.update');
    Route::delete('/product-charge-destroy', [DeliveryChargeController::class, 'destroyProduct'])->name('admin.product.charge.destroy');
    // Product
    Route::get('/box-charge', [DeliveryChargeController::class, 'indexBox'])->name('admin.box.charge');
    Route::get('/box-charge-table', [DeliveryChargeController::class, 'showBox'])->name('admin.box.charge.table');
    Route::get('/box-charge-edit/{id}', [DeliveryChargeController::class, 'editBox'])->name('admin.box.charge.edit');
    Route::get('/box-charge-create', [DeliveryChargeController::class, 'createBox'])->name('admin.box.charge.create');
    Route::post('/box-charge-store', [DeliveryChargeController::class, 'storeBox'])->name('admin.box.charge.store');
    Route::put('/box-charge-update/{id}', [DeliveryChargeController::class, 'updateBox'])->name('admin.box.charge.update');
    Route::delete('/box-charge-destroy', [DeliveryChargeController::class, 'destroyBox'])->name('admin.box.charge.destroy');
});
