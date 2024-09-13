<?php

namespace App\Services;

use App\Entities\Order;
use App\Repositories\OrderInvoiceValues;
use App\Repositories\Orders;
use App\Services\Label\AddLabelService;

class CalculateSubjectInvoiceBilansLabels {
    public static function handle(Order $order): void
    {
        if ($order->labels->contains(287)) {
            return;
        }

        if ($order->payments()->count() === 0) {
            return;
        }


        if ($order->labels->contains(66)) {
            $orderInvoiceValuesSum = OrderInvoiceValues::getSumOfInvoiceValuesByOrder($order);
            $orderValue = $order->getValue() + Orders::getOrderReturnGoods($order) - Orders::getSumOfWTONPayments($order);
            $arr = [];

            if (round($orderInvoiceValuesSum, 2) != round($orderValue, 2) && !$order->labels->contains(124)) {
                AddLabelService::addLabels($order, [231], $arr, []);

                $order->labels()->detach(232);
                return;
            }

            AddLabelService::addLabels($order, [232], $arr, []);
            $order->labels()->detach(231);
        }
    }
}
