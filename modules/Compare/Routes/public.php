<?php

use Illuminate\Support\Facades\Route;

Route::get('compare', 'CompareController@index')->name('compare.index');
Route::post('compare', 'CompareController@store')->name('compare.store');
Route::delete('compare/{productId}', 'CompareController@destroy')->name('compare.destroy');
Route::get('compare/related-products', 'CompareRelatedProductController@index')->name('compare.related_products.index');




Route::prefix('api/v1')->middleware(['web'])->group(function () {
    Route::get('compare', 'CompareController@apiIndex');
    Route::post('compare', 'CompareController@apiStore');
    Route::delete('compare/{productId}', 'CompareController@apiDestroy');
    Route::get('compare/related-products', 'CompareRelatedProductController@apiRelatedProductsIndex');

});

//Route::prefix('api/v1')->middleware(['web'])->group(function () {
//    Route::prefix('compare')->group(function () {
//        Route::get('/', [CompareController::class, 'apiIndex'])->name('api.compare.index');
//        Route::post('/', [CompareController::class, 'apiStore'])->name('api.compare.store');
//        Route::delete('/{productId}', [CompareController::class, 'apiDestroy'])->name('api.compare.destroy');
//        Route::get('/related-products', [CompareRelatedProductController::class, 'apiRelatedProductsIndex'])->name('api.compare.related_products.index');
//    });
//});
