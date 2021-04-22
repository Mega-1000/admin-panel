<?php

Route::get('/', ['as' => 'index', 'uses' =>'LabelGroupsController@index']);
Route::get('/datatable/', ['as' => 'datatable', 'uses' =>'LabelGroupsController@datatable']);
Route::get('/create/', ['as' => 'create', 'uses' =>'LabelGroupsController@create']);
Route::post('/store/', ['as' => 'store', 'uses' =>'LabelGroupsController@store']);
Route::get('/{id}/edit', ['as' => 'edit', 'uses' =>'LabelGroupsController@edit']);
Route::put('/{id}/update', ['as' => 'update', 'uses' => 'LabelGroupsController@update',]);
Route::delete('/{id}/', ['as' => 'destroy', 'uses' => 'LabelGroupsController@destroy',]);
