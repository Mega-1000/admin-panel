<?php

Route::get('/',  ['as' => 'index', 'uses' => 'Api\SetsController@index']);
Route::post('/', ['as' => 'store', 'uses' => 'Api\SetsController@store']);
Route::get('/{set}',  ['as' => 'index', 'uses' => 'Api\SetsController@set']);
Route::put('/{set}', ['as' => 'update', 'uses' => 'Api\SetsController@update']);
Route::delete('/{set}', ['as' => 'delete', 'uses' => 'Api\SetsController@delete']);
//
//Route::post('/{set}/usun', ['as' => 'delete', 'uses' => 'Api\SetsController@delete']);
//Route::post('/{set}/products/add', ['as' => 'addProduct', 'uses' => 'Api\SetsController@addProduct']);
//Route::post('/{set}/products/edytuj/{productSet}', ['as' => 'editProduct', 'uses' => 'Api\SetsController@editProduct']);
//Route::post('/{set}/products/{productSet}', ['as' => 'deleteProduct', 'uses' => 'Api\SetsController@deleteProduct']);
//Route::post('/{set}/completing/', ['as' => 'completingSets', 'uses' => 'Api\SetsController@completing']);
//Route::post('/{set}/disassembly/', ['as' => 'disassemblySets', 'uses' => 'Api\SetsController@disassembly']);
