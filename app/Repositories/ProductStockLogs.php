<?php

namespace App\Repositories;

use App\Entities\ProductStock;
use App\Entities\ProductStockLog;
use Carbon\Carbon;

class ProductStockLogs
{
    /**
     * Ger total quantity for product stock in last days
     *
     * @param ProductStock $productStock
     * @param int $daysBack
     * @return int
     */
    public static function getTotalQuantityForProductStockInLastDays(ProductStock $productStock, int $daysBack): int
    {
        return $productStock->logs()
            ->where('created_at', '>=', Carbon::now()->subDays($daysBack))
            ->where('action', 'DELETE')
            ->sum('quantity');
    }

    public static function getTotalQuantityForProductStockPeriod(ProductStock $productStock, int $start, int $end): int
    {
        return $productStock->logs()
            ->where('created_at', '>=', Carbon::now()->subDays($start))
            ->where('created_at', '<=', Carbon::now()->subDays($end))
            ->where('action', 'DELETE')
            ->sum('quantity');
    }
}
