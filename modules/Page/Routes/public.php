<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index')->name('home');

Route::prefix('api/v1')->group(function () {
    Route::get('sections', 'HomeController@getSections');
	Route::get('mobile-sections', 'HomeController@getMobileSections');
});
