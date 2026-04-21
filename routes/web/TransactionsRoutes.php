<?php

Route::get('/transactions',  ['as' => 'index', 'uses' => 'TransactionController@index']);

