<?php

Route::get('/content/delete', ['as'=> 'deleteContent', 'uses' => 'PagesGeneratorController@deleteContent']);
Route::get('/{id}/content/edit', ['as'=> 'editContent', 'uses' => 'PagesGeneratorController@editContent']);
Route::get('/{id}/content/new', ['as'=> 'newContent', 'uses' => 'PagesGeneratorController@newContent']);
Route::post('/{id}/content/store', ['as'=> 'saveContent', 'uses' => 'PagesGeneratorController@storeContent']);
Route::get('/{id}/content', ['as'=> 'list', 'uses' => 'PagesGeneratorController@contentList']);
Route::get('/new', ['as'=> 'index', 'uses' => 'PagesGeneratorController@createPage']);
Route::post('/store', ['as'=> 'index', 'uses' => 'PagesGeneratorController@store']);
Route::get('/{id}/delete', ['as'=> 'index', 'uses' => 'PagesGeneratorController@delete']);
Route::get('/{id}', ['as'=> 'index', 'uses' => 'PagesGeneratorController@edit']);
Route::get('/', ['as'=> 'index', 'uses' => 'PagesGeneratorController@getPages']);
