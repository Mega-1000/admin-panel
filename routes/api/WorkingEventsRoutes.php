<?php

Route::get('/events',  ['as' => 'index', 'uses' => 'Api\WorkingEventsController@index']);
Route::get('/inactivity',  ['as' => 'inactivity', 'uses' => 'Api\WorkingEventsController@inactivity']);
Route::get('/workers',  ['as' => 'workers', 'uses' => 'Api\WorkingEventsController@workers']);
Route::delete('/{trackerLogs}',  ['as' => 'delete', 'uses' => 'Api\WorkingEventsController@destroy']);

