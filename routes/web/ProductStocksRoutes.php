<?php

use Illuminate\Support\Facades\Route;

Route::prefix('products/stocks')->group(function () {
    Route::get('products/stocks', 'ProductStocksController@index')->name('product_stocks.index');
    Route::post('products/stocks/datatable', 'ProductStocksController@datatable')->name('product_stocks.datatable');
    Route::get('products/stocks/print', 'ProductStocksController@print')->name('product_stocks.print');
    Route::get('products/stocks/printReport', 'ProductStocksController@printReport')->name('product_stocks.printReport');
    Route::get('products/stocks/{id}/edit', 'ProductStocksController@edit')->name('product_stocks.edit');
    Route::put('products/stocks/{id}/update', 'ProductStocksController@update')->name('product_stocks.update');
    Route::put('products/stocks/{id}/change-status',
        'ProductStocksController@changeStatus')->name('product_stocks.change.status');
    Route::get('products/stocks/{id}/positions/create',
        'ProductStockPositionsController@create')->name('product_stocks.position.create');
    Route::get('products/stocks/{id}/positions/datatable',
        'ProductStockPositionsController@datatable')->name('product_stocks.position.datatable');
    Route::post('products/stocks/{id}/positions/store',
        'ProductStockPositionsController@store')->name('product_stocks.position.store');
    Route::get('products/stocks/{id}/positions/{position_id}/edit',
        'ProductStockPositionsController@edit')->name('product_stocks.position.edit');
    Route::put('products/stocks/{id}/positions/{position_id}/update',
        'ProductStockPositionsController@update')->name('product_stocks.position.update');
    Route::delete('products/stocks/{id}/positions/{position_id}',
        'ProductStockPositionsController@destroy')->name('product_stocks.position.destroy');
    Route::get('products/stocks/{id}/logs/datatable',
        'ProductStockLogsController@datatable')->name('product_stocks.logs.datatable');
    Route::get('products/stocks/{id}/logs/{log_id}/show',
        'ProductStockLogsController@show')->name('product_stocks.logs.show');
    Route::get('products/stocks/packets/create',
        'ProductStockPacketsController@create')->name('product_stock_packets.create');
    Route::post('products/stocks/packets',
        'ProductStockPacketsController@store')->name('product_stock_packets.store');
    Route::delete('products/stocks/packets/{packetId}',
        'ProductStockPacketsController@delete')->name('product_stock_packets.delete');
    Route::get('products/stocks/packets',
        'ProductStockPacketsController@index')->name('product_stock_packets.index');
    Route::get('orders/{orderId}/packet/{packetId}/use',
        'OrdersController@usePacket')->name('orders.usePacket');
    Route::post('products/stocks/packets/{packetId}/orderItem/{orderItemId}/assign',
        'Api\ProductStockPacketsController@assign')->name('product_stock_packets.assign');
    Route::post('products/stocks/packets/orderItem/{orderItemId}/retain',
        'Api\ProductStockPacketsController@retain')->name('product_stock_packets.retain');
    Route::get('products/stocks/packets/product/stock/check',
        'Api\ProductStockPacketsController@checkProductStockForPacketAssign')->name('product_stock_packets.product.stock.check');
    Route::get('products/stocks/packets/{packetId}',
        'ProductStockPacketsController@edit')->name('product_stock_packets.edit');
    Route::put('products/stocks/packets',
        'ProductStockPacketsController@update')->name('product_stock_packets.update');

    Route::get('{productStock:product_id}/place-admin-order',
        'ProductStocksController@placeAdminSideOrder')->name('product_stocks.placeAdminOrder');
    Route::get('{productStock:product_id}/place-admin-order/calculate',
        'ProductStocksController@calculateAdminOrder')->name('product_stocks.calculateAdminOrder');
    Route::post('{productStock:product_id}/place-admin-order/confirm',
        'ProductStocksController@createAdminOrder')->name('product_stocks.createAdminOrder');

    Route::get('place-multiple-admin-orders',
        'ProductStocksController@placeMultipleAdminSideOrders')->name('product_stocks.placeMultipleAdminOrders');
    Route::get('place-multiple-admin-orders/calculate',
        'ProductStocksController@calculateMultipleAdminOrders')->name('product_stocks.calculateMultipleAdminOrders');
    Route::post('place-multiple-admin-orders/confirm', 'ProductStocksController@createMultipleAdminOrders')
        ->name('product_stocks.createMultipleAdminOrders');
});


