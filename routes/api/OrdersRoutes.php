<?php

Route::get('/dates',  ['as' => 'dates', 'uses' => 'Api\OrdersController@dates']);
