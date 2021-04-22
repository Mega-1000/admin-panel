<?php

Route::get('/', ['as' => 'index', 'uses' =>'StatusesController@index']);
Route::get('/datatable/', ['as' => 'datatable', 'uses' =>'StatusesController@datatable']);
Route::get('/create/', ['as' => 'create', 'uses' =>'StatusesController@create']);
Route::post('/store/', ['as' => 'store', 'uses' =>'StatusesController@store']);
Route::get('/{id}/edit', ['as' => 'edit', 'uses' =>'StatusesController@edit']);
Route::put('/{id}/update', ['as' => 'update', 'uses' => 'StatusesController@update']);
Route::delete('/{id}/', ['as' => 'destroy', 'uses' => 'StatusesController@destroy']);
Route::put('/{id}/change-status', ['as' => 'change.status', 'uses' => 'StatusesController@changeStatus']);
