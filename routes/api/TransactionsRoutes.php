<?php

Route::get('/transactions',  ['as' => 'transactions', 'uses' => 'Api\TransactionsController@index']);
