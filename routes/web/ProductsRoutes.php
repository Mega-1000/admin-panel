<?php

Route::get('/', ['as' => 'index', 'uses' => 'ProductStocksController@index']);
Route::post('/datatable', ['as' => 'datatable', 'uses' => 'ProductStocksController@datatable']);
Route::get('/print', ['as' => 'print', 'uses' => 'ProductStocksController@print']);
Route::get('/{id}/edit', ['as' => 'edit', 'uses' => 'ProductStocksController@edit']);
Route::put('/{id}/update', ['as' => 'update', 'uses' => 'ProductStocksController@update']);
Route::put('/{id}/change-status', ['as' => 'change.status', 'uses' => 'ProductStocksController@changeStatus']);
Route::get('/{id}/positions/create', ['as' => 'position.create', 'uses' => 'ProductStockPositionsController@create']);
Route::get('/{id}/positions/datatable', ['as' => 'position.datatable', 'uses' => 'ProductStockPositionsController@datatable']);
Route::post('/{id}/positions/store', ['as' => 'position.store', 'uses' => 'ProductStockPositionsController@store']);
Route::get('/{id}/positions/{position_id}/edit', ['as' => 'position.edit', 'uses' => 'ProductStockPositionsController@edit']);
Route::put('/{id}/positions/{position_id}/update', ['as' => 'position.update', 'uses' => 'ProductStockPositionsController@update']);
Route::delete('/{id}/positions/{position_id}', ['as' => 'position.destroy', 'uses' => 'ProductStockPositionsController@destroy']);
Route::get('/{id}/logs/datatable', ['as' => 'index', 'logs.datatable' => 'ProductStockLogsController@datatable']);
Route::get('/{id}/logs/{log_id}/show', ['as' => 'logs.show', 'uses' => 'ProductStockLogsController@show']);
