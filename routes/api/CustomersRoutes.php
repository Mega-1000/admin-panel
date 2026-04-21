<?php

Route::post('/getCustomers',  ['as' => 'getCustomers', 'uses' => 'Api\CustomersController@getCustomers']);
