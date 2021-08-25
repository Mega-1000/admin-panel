<?php

Route::get('/index',  ['as' => 'index', 'uses' => 'Api\TransactionsController@index']);
