<?php

use Illuminate\Support\Facades\Route;

Route::get('countries/{code}/states', 'CountryStateController@index')->name('countries.states.index');
Route::get('states/{code}/cities', 'CountryStateController@getStateCities')->name('states.cities.index');
Route::get('cities/{code}/zones', 'CountryStateController@getCitiesZones')->name('cities.zones.index');

Route::get('manifest.json', 'ManifestController@json')->name('manifest.json');

Route::get('offline', 'ManifestController@offline')->name('offline');

Route::get('sitemap', 'SitemapController@index')->name('sitemap');
