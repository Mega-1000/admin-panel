<?php

Route::get('/',  ['as' => 'index', 'uses' => 'Api\TransactionsController@index']);
Route::post('/',  ['as' => 'store', 'uses' => 'Api\TransactionsController@store']);
Route::delete('/{transaction}',  ['as' => 'delete', 'uses' => 'Api\TransactionsController@destroy']);
Route::put('/{transaction}',  ['as' => 'update', 'uses' => 'Api\TransactionsController@update']);
Route::post('/import/{kind}',  ['as' => 'import', 'uses' => 'Api\TransactionsController@import']);
