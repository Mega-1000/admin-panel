<?php

namespace App\Providers\Entities;

use App\Entities\OrderMessage;
use App\Observers\Entities\OrderMessageObserver;
use Illuminate\Support\ServiceProvider;

class OrderMessageModelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        OrderMessage::observe(OrderMessageObserver::class);
    }
}
