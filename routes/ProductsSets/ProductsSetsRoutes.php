<?php
use Illuminate\Http\Request;

Route::group(['prefix' => 'admin'], function () {
    Route::group(['prefix' => 'products/sets', 'middleware' => 'admin', 'as' => 'sets.'], function () {
        Route::get('/',  ['as' => 'index', 'uses' => 'SetsController@index']);
        Route::get('/nowy', ['as' => 'create', 'uses' => 'SetsController@create']);
        Route::post('/nowy', ['as' => 'store', 'uses' => 'SetsController@store']);
        Route::get('/{set}/edytuj', ['as' => 'edit', 'uses' => 'SetsController@edit']);
        Route::post('/{set}/edytuj', ['as' => 'update', 'uses' => 'SetsController@update']);
        Route::post('/{set}/usun', ['as' => 'delete', 'uses' => 'SetsController@delete']);
        Route::post('/{set}/products/add', ['as' => 'addProduct', 'uses' => 'SetsController@addProduct']);
        Route::post('/{set}/products/edytuj/{productSet}', ['as' => 'editProduct', 'uses' => 'SetsController@editProduct']);
        Route::post('/{set}/products/{productSet}', ['as' => 'deleteProduct', 'uses' => 'SetsController@deleteProduct']);

    });
});
