<?php

namespace App\Repositories;

use App\Entities\OrderItem;
use Illuminate\Database\Eloquent\Collection;

class OrderItems
{
    public static function getItemsWithProductsWithLowOrderQuantityAlertText(int $orderId): Collection
    {
        return OrderItem::with('product')
            ->whereHas('product', function ($query) {
                $query->whereNotNull('low_order_quantity_alert_text');
            })
            ->where('order_id', $orderId)
            ->get();
    }
}
