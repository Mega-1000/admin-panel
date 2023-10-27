<?php

namespace App\Services;

use App\Entities\LowOrderQuantityAlert;
use App\Entities\LowOrderQuantityAlertMessage;
use App\Entities\Order;
use App\Entities\OrderItem;
use App\Helpers\LowOrderQuantityAlertsSpacesHelper;
use App\Jobs\AlertForOrderLowQuantityJob;
use App\Repositories\OrderItems;

class LowOrderQuantityAlertService
{
    /**
     * Dispatch alerts for order
     *
     * @param Order $order
     * @return void
     */
    public function dispatchAlertsForOrder(Order $order): void
    {
        LowOrderQuantityAlert::all()->each(function (LowOrderQuantityAlert $alert) use (&$order) {
            $finalQuantity = 0;

            $orderItems = OrderItems::getItemsWithProductsWithLowOrderQuantityAlertText($order->id);

            foreach ($orderItems as $item) {
                /** @var Order $order */
                $order = $item->order;

                $columnName = $alert->column_name;
                if (
                    in_array(
                        $item->product->$columnName,
                        explode(',', $alert->item_names)
                    )
                ) {
                    $finalQuantity += $item->quantity;
                }
            }

            if ($finalQuantity !== 0 && $finalQuantity < $alert->min_quantity) {
                /** @var LowOrderQuantityAlertMessage $message */
                foreach ($alert->messages as $message) {
                    dispatch(new AlertForOrderLowQuantityJob($order, $message))->delay(Carbon::now()->addHours($message->delay_time));
                }
            }
        });
    }

    public static function parseToken(string $text, int $orderId): string
    {
        return str_replace('{idZamowienia}', $orderId, $text);
    }
}
