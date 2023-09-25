<?php

namespace App\Services;

use App\Entities\LowOrderQuantityAlert;
use App\Entities\Order;
use App\Entities\OrderItem;
use App\Jobs\AlertForOrderLowQuantityJob;

class LowOrderQuantityAlertService
{
    public function dispatchAlertsForOrder(Order $order): void
    {
        LowOrderQuantityAlert::all()->each(function (LowOrderQuantityAlert $alert) use (&$order) {
            $finalQuantity = 0;

            $orderItems = OrderItem::with('product')
                ->whereHas('product', function ($query) use ($alert) {
                    $query->whereNotNull('low_order_quantity_alert_text');
                })
                ->where('order_id', $order->id)
                ->get();

            foreach ($orderItems as $item) {
                if ($item->product->low_order_quantity_alert_text === $alert->item_names) {
                    $finalQuantity += $item->quantity;
                }
            }

            if ($finalQuantity !== 0 && $finalQuantity < $alert->min_quantity) {
                dispatch(new AlertForOrderLowQuantityJob($order, $alert))->delay(now()->addHours($alert->delay_time));
            }
        });
    }

    public static function parseToken(string $text, int $orderId): string
    {
        return str_replace('{idZamowienia}', $orderId, $text);
    }
}
