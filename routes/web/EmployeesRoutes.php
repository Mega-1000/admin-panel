<?php

Route::get('/datatable/{id}', ['as' => 'datatable', 'uses' =>'EmployeesController@datatable']);
Route::get('/create/{firm_id}', ['as' => 'create', 'uses' =>'EmployeesController@create']);
Route::post('/store/{firm_id}', ['as' => 'store', 'uses' =>'EmployeesController@store']);
Route::get('/{id}/edit', ['as' => 'edit', 'uses' =>'EmployeesController@edit']);
Route::put('/{id}/update', ['as' => 'update', 'uses' => 'EmployeesController@update',]);
Route::delete('/{id}/', ['as' => 'destroy', 'uses' => 'EmployeesController@destroy',]);
Route::put('/{id}/change-status', ['as' => 'change.status', 'uses' => 'EmployeesController@changeStatus',]);
