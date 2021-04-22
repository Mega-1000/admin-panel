<?php

Route::get('/', ['as' => 'index', 'uses' => 'EmployeeRoleController@index']);
Route::get('/datatable', ['as' => 'datatable', 'uses' => 'EmployeeRoleController@datatable']);
Route::get('/create', ['as' => 'create', 'uses' => 'EmployeeRoleController@create']);
Route::post('/store', ['as' => 'store', 'uses' => 'EmployeeRoleController@store']);
Route::get('/{id}/edit', ['as' => 'edit', 'uses' => 'EmployeeRoleController@edit']);
Route::put('/{id}/update', ['as' => 'update', 'uses' => 'EmployeeRoleController@update']);
Route::delete('/{id}/delete', ['as' => 'destroy', 'uses' => 'EmployeeRoleController@destroy']);
