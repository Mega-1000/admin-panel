<?php

Route::get('{order}/getDates', 'Api\OrdersController@getDates')->name('api.orders.get-dates');
Route::put('{order}/acceptDates', 'Api\OrdersController@acceptDates')->name('api.orders.accept-dates');
Route::put('{order}/updateDates', 'Api\OrdersController@updateDates')->name('api.orders.update-dates');
