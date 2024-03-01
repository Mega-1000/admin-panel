<?php

use App\Entities\ChatAuction;
use App\Http\Controllers\AuctionsController;

Route::prefix('auctions/{chat}')->name('auctions.')->group(function () {
    Route::get('create', [AuctionsController::class, 'create'])->name('create');
    Route::post('create', [AuctionsController::class, 'store'])->name('store');
});

Route::get('success', [AuctionsController::class, 'success'])->name('success');

Route::prefix('auctions/{auction}')->name('auctions.')->group(function () {
    Route::get('show', [AuctionsController::class, 'show'])->name('show');
    Route::post('confirm', [AuctionsController::class, 'confirm'])->name('confirm')->middleware('auth');
    Route::get('end', [AuctionsController::class, 'end'])->name('end');
    Route::post('end', [AuctionsController::class, 'placeOrderForAuction'])->name('place-order-for-auction');
    Route::post('end-create-orders', [AuctionsController::class, 'endCreateOrders'])->name('end-create-orders');
    Route::put('edit', [AuctionsController::class, 'update'])->name('edit');
});

Route::get('auctions/offer/create/{token}', [AuctionsController::class, 'createOffer'])->name('auctions.offer.create');
Route::post('auctions/offer/store/{token}', [AuctionsController::class, 'storeOffer'])->name('auctions.offer.store');
Route::post('auctions/send-notification-about-firm-panel/{firm}', [AuctionsController::class, 'sendNotificationAboutFirmPanel'])->name('auction.notification-firm-panel');
Route::get('auctions/display-pre-data-table/{chat}', [AuctionsController::class, 'displayPreDataPricesTable'])->name('displayPreDataPricesTableForOrder');
Route::get('auctions/display-prices-table',  [AuctionsController::class, 'displayPricesTable'])->name('displayPreDataPricesTable');
Route::get('auctions/get-styrofoam-types', [AuctionsController::class, 'getStyrofoamTypes']);
Route::get('auctions/get-quotes-by-styrofoarm-type/{type}', [AuctionsController::class, 'getQuotesByStyrofoamType']);

Route::post('end-auction/{auctionId}', [AuctionsController::class, 'endAuctionStore'])->name('end-auction.store');

