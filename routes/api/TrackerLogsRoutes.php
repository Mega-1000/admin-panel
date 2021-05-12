<?php
Route::get('/logs',  ['as' => 'index', 'uses' => 'Api\TrackerLogsController@index']);
