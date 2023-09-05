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
        $returns = [];

        if ($product) {
            $res = 0;

            $orders = Order::query()
                ->whereHas('items', function ($query) use ($product) {
                    $query->where('product_id', $product->id);
                })
                ->with('items', 'labels', 'orderReturn')
                ->where('created_at', '>=', Carbon::now()->subDays($daysBack))
                ->where('created_at', '<=', Carbon::now())
                ->whereDoesntHave('customer', function ($query) {
                    $query->where('login', 'info@mega1000.pl');
                })
                ->get();

            foreach ($orders as $order) {
                $labels = $order->labels->pluck('id')->toArray();
                if (in_array(50, $labels) && in_array(179, $labels)) {
                    $returns[] = $order->orderReturn->flatten();
                }

                foreach ($order->items as $item) {
                    if ($item->product_id == $product->id) {
                        $res += $item->quantity;
                    }
                }

                foreach ($returns as $return) {
                    foreach ($return as $item) {
                        if ($item->product_id == $product->id) {
                            $res -= $item->quantity;
                        }
                    }
                }
            }
        }

        return $res ?? 0;
    }

    public static function getTotalQuantityForProductStockPeriod(ProductStock $productStock, int $start, int $end): int
    {
        $product = $productStock->product;
        $res = 0;

        $orders = Order::query()
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->with('items')
            ->where('created_at', '>=', Carbon::now()->subDays($start))
            ->where('created_at', '<=', Carbon::now()->subDays($end))
            ->get();

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                if ($item->product_id == $product->id) {
                    $res += $item->quantity;
                }
            }
        }

        return $res;
    }
}
