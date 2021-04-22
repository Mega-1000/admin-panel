<?php

Route::get('/datatable/{id}', ['as' => 'datatable', 'uses' =>'WarehousesController@datatable']);
Route::get('/create/{firm_id}', ['as' => 'create', 'uses' =>'WarehousesController@create']);
Route::post('/store/{firm_id}', ['as' => 'store', 'uses' =>'WarehousesController@store']);
Route::get('/{id}/edit', ['as' => 'edit', 'uses' =>'WarehousesController@edit']);
Route::put('/{id}/update', ['as' => 'update', 'uses' => 'WarehousesController@update',]);
Route::delete('/{id}/', ['as' => 'destroy', 'uses' => 'WarehousesController@destroy',]);
Route::put('/{id}/change-status', ['as' => 'change.status', 'uses' => 'WarehousesController@changeStatus',]);
Route::get('/search/autocomplete', ['uses' =>'WarehousesController@autocomplete']);
Route::get('/{symbol}/editBySymbol', ['uses' =>'WarehousesController@editBySymbol']);

Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
    Route::get('/new', ['as' => 'index', 'uses' => 'WarehouseOrdersController@index']);
    Route::post('/datatable', ['as' => 'datatable', 'uses' => 'WarehouseOrdersController@datatable']);
    Route::post('/datatable/all', ['as' => 'datatable.all', 'uses' => 'WarehouseOrdersController@datatableAll']);
    Route::post('/makeOrder', ['as' => 'makeOrder', 'uses' => 'WarehouseOrdersController@makeOrder']);
    Route::get('/{id}/edit', ['as' => 'edit', 'uses' => 'WarehouseOrdersController@edit']);
    Route::put('/{id}/update', ['as' => 'update', 'uses' => 'WarehouseOrdersController@update']);
    Route::get('/', ['as' => 'all', 'uses' => 'WarehouseOrdersController@all']);
    Route::post('/sendEmail', ['as' => 'sendEmail', 'uses' => 'WarehouseOrdersController@sendEmail']);
});
