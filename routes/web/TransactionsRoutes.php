<?php

Route::get('/transactions',  ['as' => 'index', 'uses' => 'TransactionController@index']);
Route::get('/transactions/create',  ['as' => 'create', 'uses' => 'TransactionController@create']);
Route::get('/transaction/datatable', ['as' => 'datatable', 'uses' => 'TransactionController@datatable'])->name('transaction.datatable');

