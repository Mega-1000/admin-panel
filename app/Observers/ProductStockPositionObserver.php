<?php

namespace App\Observers;

use App\Entities\ProductStock;
use App\Entities\ProductStockPosition;

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
        $stockQuantity = ProductStockPosition::where('product_stock_id', $productStockPosition->product_stock_id)
            ->sum('product_stock_id');

        $productStock = ProductStock::find($productStockPosition->product_stock_id);
        $productStock->quantity = $stockQuantity;
        $productStock->save();
    }
}
