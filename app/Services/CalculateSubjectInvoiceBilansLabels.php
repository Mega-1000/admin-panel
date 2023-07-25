<?php

namespace App\Services;

use App\Entities\Order;
use App\Repositories\OrderInvoiceValues;
use App\Repositories\Orders;
use App\Services\Label\AddLabelService;

class CalculateSubjectInvoiceBilansLabels {
    public static function handle(Order $order): void
    {
        $orderInvoiceValuesSum = OrderInvoiceValues::getSumOfInvoiceValuesByOrder($order);
        $orderValue = $order->getValue() + Orders::getOrderReturnGoods($order) - Orders::getSumOfWTONPayments($order);
        $arr = [];
        if (round($orderInvoiceValuesSum, 2) != round($orderValue, 2)) {
            AddLabelService::addLabels($order, [231],$arr, []);

            $order->labels()->detach(232);
            return;
        }

        AddLabelService::addLabels($order, [232],$arr, []);
        $order->labels()->detach(231);
    }
}
