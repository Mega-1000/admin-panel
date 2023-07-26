<?php

namespace App\Repositories;

use App\Entities\Order;
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
        $product = $productStock->product;
        $res = 0;

        $orders = Order::query()
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->with('items')
            ->where('created_at', '>=', Carbon::now()->subDays($daysBack))
            ->where('created_at', '<=', Carbon::now())
            ->get();

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                if ($item->product_id == $product->id) {
                    $res += $item->amount;
                }
            }
        }

        return $res;
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
