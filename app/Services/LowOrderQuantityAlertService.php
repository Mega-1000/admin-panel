<?php

namespace App\Services;

use App\Entities\LowOrderQuantityAlert;
use App\Entities\Order;
use App\Entities\OrderItem;
use App\Helpers\LowOrderQuantityAlertsSpacesHelper;
use App\Jobs\AlertForOrderLowQuantityJob;
use App\Repositories\OrderItems;

class LowOrderQuantityAlertService
{
    public function dispatchAlertsForOrder(Order $order): void
    {
        LowOrderQuantityAlert::all()->each(function (LowOrderQuantityAlert $alert) use (&$order) {
            $finalQuantity = 0;

            $orderItems = OrderItems::getItemsWithProductsWithLowOrderQuantityAlertText($order->id);

            foreach ($orderItems as $item) {
                /** @var Order $order */
                $order = $item->order;

                if (!LowOrderQuantityAlertsSpacesHelper::checkIfSpaceIsCorrect($alert, $order)) {
                    continue;
                }

                if (
                    in_array(
                        $alert->item_names,
                        explode($item->product->low_order_quantity_alert_text, ',')
                    )
                ) {
                    $finalQuantity += $item->quantity;
                }
            }

            if ($finalQuantity !== 0 && $finalQuantity < $alert->min_quantity) {
                dispatch(new AlertForOrderLowQuantityJob($order, $alert))
                    ->delay(now()->addHours($alert->delay_time));
            }
        });
    }

    public static function parseToken(string $text, int $orderId): string
    {
        return str_replace('{idZamowienia}', $orderId, $text);
    }
}
