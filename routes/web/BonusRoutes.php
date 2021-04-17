<?php

Route::get('/', ['as'=> 'index', 'uses' => 'BonusController@index']);
Route::post('/', ['as'=> 'create', 'uses' => 'BonusController@create']);
Route::post('/delete', ['as'=> 'destroy', 'uses' => 'BonusController@destroy']);
