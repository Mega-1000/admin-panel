<?php

namespace App\Providers\Entities;

use App\Entities\Order;
use App\Entities\ProductStockPosition;
use App\Observers\Entities\OrderObserver;
use App\Observers\ProductStockPositionObserver;
use Illuminate\Support\ServiceProvider;

class ProductStockPositionModelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        ProductStockPosition::observe(ProductStockPositionObserver::class);
    }
}
