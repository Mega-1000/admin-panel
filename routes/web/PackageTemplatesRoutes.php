<?php

Route::get('/', ['as' => 'index', 'uses' => 'PackageTemplatesController@index']);
Route::get('/datatable', ['as' => 'datatable', 'uses' => 'PackageTemplatesController@datatable']);
Route::get('/create', ['as' => 'create', 'uses' => 'PackageTemplatesController@create']);
Route::post('/store', ['as' => 'store', 'uses' => 'PackageTemplatesController@store']);
Route::get('/{id}/edit', ['as' => 'edit', 'uses' => 'PackageTemplatesController@edit']);
Route::put('/{id}/update', ['as' => 'update', 'uses' => 'PackageTemplatesController@update']);
Route::delete('/{id}/delete', ['as' => 'destroy', 'uses' => 'PackageTemplatesController@destroy']);
Route::get('/{id}/data', ['as' => 'getPackageTemplate', 'uses' => 'PackageTemplatesController@getPackageTemplate']);
