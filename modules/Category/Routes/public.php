<?php

use Illuminate\Support\Facades\Route;

Route::get('categories', 'CategoryController@index')->name('categories.index');

Route::get('categories/{category}/products', 'CategoryProductController@index')->name('categories.products.index');


Route::prefix('api/v1')->group(function () {
    Route::get('categories', 'CategoryController@apiIndex');
    Route::get('categories/{category}/products', 'CategoryProductController@apiIndex');
});
