<?php

use Illuminate\Support\Facades\Route;

Route::prefix('products/stocks')->group(function () {

    Route::get('/', 'ProductStocksController@index')->name('product_stocks.index');
    Route::post('datatable', 'ProductStocksController@datatable')->name('product_stocks.datatable');
    Route::get('print', 'ProductStocksController@print')->name('product_stocks.print');
    Route::get('printReport', 'ProductStocksController@printReport')->name('product_stocks.printReport');
    Route::get('{id}/edit', 'ProductStocksController@edit')->name('product_stocks.edit');
    Route::put('{id}/update', 'ProductStocksController@update')->name('product_stocks.update');
    Route::put('{id}/change-status', 'ProductStocksController@changeStatus')->name('product_stocks.change.status');

    Route::prefix('{id}/positions')->group(function () {
        Route::get('create', 'ProductStockPositionsController@create')->name('product_stocks.position.create');
        Route::get('datatable', 'ProductStockPositionsController@datatable')->name('product_stocks.position.datatable');
        Route::post('store', 'ProductStockPositionsController@store')->name('product_stocks.position.store');
        Route::get('{position_id}/edit', 'ProductStockPositionsController@edit')->name('product_stocks.position.edit');
        Route::put('{position_id}/update', 'ProductStockPositionsController@update')->name('product_stocks.position.update');
        Route::delete('{position_id}', 'ProductStockPositionsController@destroy')->name('product_stocks.position.destroy');
    });

    Route::prefix('{id}/logs')->group(function () {
        Route::get('datatable', 'ProductStockLogsController@datatable')->name('product_stocks.logs.datatable');
        Route::get('{log_id}/show', 'ProductStockLogsController@show')->name('product_stocks.logs.show');
    });

    Route::prefix('packets')->group(function () {
        Route::get('create', 'ProductStockPacketsController@create')->name('product_stock_packets.create');
        Route::post('/', 'ProductStockPacketsController@store')->name('product_stock_packets.store');
        Route::delete('{packetId}', 'ProductStockPacketsController@delete')->name('product_stock_packets.delete');
        Route::get('/', 'ProductStockPacketsController@index')->name('product_stock_packets.index');
        Route::get('{packetId}', 'ProductStockPacketsController@edit')->name('product_stock_packets.edit');
        Route::put('/', 'ProductStockPacketsController@update')->name('product_stock_packets.update');
        Route::post('{packetId}/orderItem/{orderItemId}/assign', 'Api\ProductStockPacketsController@assign')->name('product_stock_packets.assign');
        Route::post('orderItem/{orderItemId}/retain', 'Api\ProductStockPacketsController@retain')->name('product_stock_packets.retain');
        Route::get('product/stock/check', 'Api\ProductStockPacketsController@checkProductStockForPacketAssign')->name('product_stock_packets.product.stock.check');
    });

    Route::get('{productStock:product_id}/place-admin-order',
        'ProductStocksController@placeAdminSideOrder')->name('product_stocks.placeAdminOrder');
    Route::get('{productStock:product_id}/place-admin-order/calculate',
        'ProductStocksController@calculateAdminOrder')->name('product_stocks.calculateAdminOrder');
    Route::post('{productStock:product_id}/place-admin-order/confirm',
        'ProductStocksController@createAdminOrder')->name('product_stocks.createAdminOrder');
});


