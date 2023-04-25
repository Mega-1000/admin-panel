<?php

use App\Http\Controllers\AuctionsController;

Route::prefix('auctions/{chat}')->name('auctions.')->group(function () {
    Route::get('create', [AuctionsController::class, 'create'])->name('create');
    Route::post('create', [AuctionsController::class, 'store'])->name('store');
});

Route::get('success', [AuctionsController::class, 'success'])->name('success');

Route::prefix('auctions/{auction}')->name('auctions.')->group(function () {
    Route::get('show', [AuctionsController::class, 'show'])->name('show');
    Route::post('confirm', [AuctionsController::class, 'confirm'])->name('confirm')->middleware('auth');
});

Route::get('auctions/offer/create/{token}', [AuctionsController::class, 'createOffer'])->name('auctions.offer.create');
Route::post('auctions/offer/store/{token}', [AuctionsController::class, 'storeOffer'])->name('auctions.offer.store');

