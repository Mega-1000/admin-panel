<?php

Route::get('/', ['as' => 'index', 'uses' =>'FirmsController@index']);
Route::get('/datatable', ['as' => 'datatable', 'uses' =>'FirmsController@datatable']);
Route::get('/create', ['as' => 'create', 'uses' =>'FirmsController@create']);
Route::post('/store', ['as' => 'store', 'uses' =>'FirmsController@store']);
Route::get('/{id}/edit', ['as' => 'edit', 'uses' =>'FirmsController@edit']);
Route::put('/{id}/update', ['as' => 'update', 'uses' => 'FirmsController@update',]);
Route::delete('/{id}/', ['as' => 'destroy', 'uses' => 'FirmsController@destroy',]);
Route::put('/{id}/change-status', ['as' => 'change.status', 'uses' => 'FirmsController@changeStatus',]);
Route::get('/{id}/sendRequestToUpdateFirmData', ['as' => 'sendRequestToUpdateFirmData', 'uses' =>'FirmsController@sendRequestToUpdateFirmData']);
