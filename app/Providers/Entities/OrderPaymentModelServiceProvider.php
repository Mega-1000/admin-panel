<?php

namespace App\Providers\Entities;

use App\Entities\OrderPayment;
use App\Observers\Entities\OrderPaymentObserver;
use Illuminate\Support\ServiceProvider;

class OrderPaymentModelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        OrderPayment::observe(OrderPaymentObserver::class);
    }
}
