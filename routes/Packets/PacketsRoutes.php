<?php
use Illuminate\Http\Request;

Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => 'admin'], function () {
        Route::group(['as' => 'product_stock_packets.'], function () {
            Route::get('products/stocks/packets/create', 'ProductStockPacketsController@create')->name('create');
            Route::post('products/stocks/packets', 'ProductStockPacketsController@store')->name('store');
            Route::delete('products/stocks/packets/{packetId}', 'ProductStockPacketsController@delete')->name('delete');
            Route::get('products/stocks/packets', 'ProductStockPacketsController@index')->name('index');
            Route::post('products/stocks/packets/{packetId}/orderItem/{orderItemId}/assign', 'Api\ProductStockPacketsController@assign')->name('assign');
            Route::post('products/stocks/packets/orderItem/{orderItemId}/retain', 'Api\ProductStockPacketsController@retain')->name('retain');
            Route::get('products/stocks/packets/product/stock/check', 'Api\ProductStockPacketsController@checkProductStockForPacketAssign')->name('product.stock.check');
            Route::get('products/stocks/packets/{packetId}', 'ProductStockPacketsController@edit')->name('edit');
            Route::put('products/stocks/packets', 'ProductStockPacketsController@update')->name('update');
        });
    });
});
