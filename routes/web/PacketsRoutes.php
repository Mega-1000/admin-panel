<?php
use Illuminate\Http\Request;

Route::get('/create', ['as' => 'create', 'uses' =>  'ProductStockPacketsController@create']);
Route::post('/', ['as' => 'store', 'uses' =>  'ProductStockPacketsController@store']);
Route::delete('/{packetId}', ['as' => 'delete', 'uses' =>  'ProductStockPacketsController@delete']);
Route::get('/', ['as' => 'index', 'uses' =>  'ProductStockPacketsController@index']);
Route::post('/{packetId}/orderItem/{orderItemId}/assign', ['as' => 'assign', 'uses' =>  'Api\ProductStockPacketsController@assign']);
Route::post('/orderItem/{orderItemId}/retain', ['as' => 'retain', 'uses' =>  'Api\ProductStockPacketsController@retain']);
Route::get('/product/stock/check', ['as' => 'check', 'uses' =>  'Api\ProductStockPacketsController@checkProductStockForPacketAssign']);
Route::get('/{packetId}', ['as' => 'edit', 'uses' =>  'ProductStockPacketsController@edit']);
Route::put('/', ['as' => 'update', 'uses' =>  'ProductStockPacketsController@update']);
