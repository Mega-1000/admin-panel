<?php

Route::get('/products',  ['as' => 'products', 'uses' => 'Api\SetsController@products']);
Route::get('/',  ['as' => 'index', 'uses' => 'Api\SetsController@index']);
Route::get('/{set}',  ['as' => 'set', 'uses' => 'Api\SetsController@set']);
Route::get('/products/{product}',  ['as' => 'product', 'uses' => 'Api\SetsController@productsStocks']);
Route::post('/', ['as' => 'store', 'uses' => 'Api\SetsController@store']);
Route::post('/{set}/completing/', ['as' => 'completing', 'uses' => 'Api\SetsController@completing']);
Route::post('/{set}/disassembly/', ['as' => 'disassembly', 'uses' => 'Api\SetsController@disassembly']);
Route::post('/{set}/products/',  ['as' => 'addProduct', 'uses' => 'Api\SetsController@addProduct']);
Route::put('/{set}', ['as' => 'update', 'uses' => 'Api\SetsController@update']);
Route::put('/{set}/products/{product}',  ['as' => 'editProduct', 'uses' => 'Api\SetsController@editProduct']);
Route::delete('/{set}', ['as' => 'delete', 'uses' => 'Api\SetsController@delete']);
Route::delete('/{set}/products/{product}',  ['as' => 'deleteProduct', 'uses' => 'Api\SetsController@deleteProduct']);
