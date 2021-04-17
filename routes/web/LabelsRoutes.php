<?php

Route::get('/', ['as' => 'index', 'uses' =>'LabelsController@index']);
Route::get('/datatable/', ['as' => 'datatable', 'uses' =>'LabelsController@datatable']);
Route::get('/create/', ['as' => 'create', 'uses' =>'LabelsController@create']);
Route::post('/store/', ['as' => 'store', 'uses' =>'LabelsController@store']);
Route::get('/{id}/edit', ['as' => 'edit', 'uses' =>'LabelsController@edit']);
Route::put('/{id}/update', ['as' => 'update', 'uses' => 'LabelsController@update']);
Route::delete('/{id}/', ['as' => 'destroy', 'uses' => 'LabelsController@destroy']);
Route::put('/{id}/change-status', ['as' => 'change.status', 'uses' => 'LabelsController@changeStatus']);
Route::get('/{id}/associated-labels-to-add-after-removal', ['as' => 'associatedLabelsToAddAfterRemoval', 'uses' => 'LabelsController@associatedLabelsToAddAfterRemoval']);
