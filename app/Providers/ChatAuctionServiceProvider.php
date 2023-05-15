<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ChatAuctionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(
            'App\Services\ChatAuctionsService',
            'App\Services\ChatAuctionsService'
        );
    }
}
