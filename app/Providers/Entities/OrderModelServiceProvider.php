<?php

namespace App\Providers\Entities;

use App\Entities\Order;
use App\Observers\Entities\OrderObserver;
use Illuminate\Support\ServiceProvider;

class OrderModelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Order::observe(OrderObserver::class);
    }
}
