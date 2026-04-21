<?php

namespace App\Providers\Entities;

use App\Entities\OrderAddress;
use App\Observers\Entities\OrderAddressObserver;
use Illuminate\Support\ServiceProvider;

class OrderAddressModelProvider extends ServiceProvider
{
    public function boot()
    {
        OrderAddress::observe(OrderAddressObserver::class);
    }
}
