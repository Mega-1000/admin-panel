<?php

Route::get('/', ['as' => 'index', 'uses' =>'CustomersController@index']);
Route::get('/datatable', ['as' => 'datatable', 'uses' =>'CustomersController@datatable']);
Route::get('/create', ['as' => 'create', 'uses' =>'CustomersController@create']);
Route::post('/store', ['as' => 'store', 'uses' =>'CustomersController@store']);
Route::get('/{id}/edit', ['as' => 'edit', 'uses' =>'CustomersController@edit']);
Route::put('/{id}/update', ['as' => 'update', 'uses' =>'CustomersController@update']);
Route::post('/{id}/override-customer-data', ['as' => 'change.login-or-password', 'uses' =>'CustomersController@changeLoginOrPassword']);
Route::delete('/{id}/', ['as' => 'destroy', 'uses' =>'CustomersController@destroy']);
Route::put('/{id}/change-status', ['as' => 'change.status', 'uses' =>'CustomersController@changeStatus']);
