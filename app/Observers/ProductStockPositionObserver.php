<?php

namespace App\Observers;

use App\Entities\ProductStock;
use App\Entities\ProductStockPosition;
use App\Services\ProductStockQuantityCalculationService;

class ProductStockPositionObserver
{
    /**
     * Handle the ProductStockPosition "updated" event.
     *
     * @param  ProductStockPosition  $productStockPosition
     * @return void
     */
    public function updated(ProductStockPosition $productStockPosition): void
    {
        ProductStockQuantityCalculationService::calculateQuantity(
            $productStockPosition
        );
    }

    /**
     * Handle the ProductStockPosition "created" event.
     *
     * @param ProductStockPosition $productStockPosition
     * @return void
     */
    public function created(ProductStockPosition $productStockPosition): void
    {
        ProductStockQuantityCalculationService::calculateQuantity(
            $productStockPosition
        );
    }
}
