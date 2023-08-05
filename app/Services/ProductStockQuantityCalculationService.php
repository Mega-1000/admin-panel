<?php

namespace App\Services;

use App\Entities\ProductStock;
use App\Entities\ProductStockPosition;

class ProductStockQuantityCalculationService
{
    public static function calculateQuantity(ProductStockPosition $productStockPosition): void
    {
        $stockQuantity = ProductStockPosition::where('product_stock_id', $productStockPosition->product_stock_id)
            ->sum('product_stock_id');

        $productStock = ProductStock::find($productStockPosition->product_stock_id);
        $productStock->quantity = $stockQuantity;
        $productStock->save();
    }
}
