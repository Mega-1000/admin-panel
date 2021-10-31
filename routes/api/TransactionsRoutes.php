<?php

Route::get('/',  ['as' => 'index', 'uses' => 'Api\TransactionsController@index']);
Route::post('/',  ['as' => 'store', 'uses' => 'Api\TransactionsController@store']);
