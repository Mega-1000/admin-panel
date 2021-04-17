<?php

Route::get('/', ['as'=> 'index', 'uses' =>'UserController@index']);
Route::get('/datatable/all', ['as'=> 'datatable', 'uses' =>'UserController@datatable']);
Route::get('/create', ['as'=> 'create', 'uses' =>'UserController@create']);
Route::post('/store', ['as'=> 'store', 'uses' =>'UserController@store']);
Route::get('/{id}/editItem', ['as'=> 'edit', 'uses' =>'UserController@edit']);
Route::put('/{id}/update', ['as'=> 'update','uses' => 'UserController@update',]);
Route::put('/{id}/change-status', ['as'=> 'change.status','uses' => 'UserController@changeStatus',]);
