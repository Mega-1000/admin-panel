<?php

Route::post('/logs',  ['as' => 'new', 'uses' => 'Api\TrackerLogsController@new']);
Route::get('/logs',  ['as' => 'index', 'uses' => 'Api\TrackerLogsController@index']);
Route::put('/logs/{log}',  ['as' => 'update', 'uses' => 'Api\TrackerLogsController@update']);
