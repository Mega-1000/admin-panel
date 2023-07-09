<?php

namespace App\Repositories;

use App\Entities\Order;

class OrderInvoiceValues
{
    /**
     * @param Order $order
     * @return float
     */
    public static function getSumOfInvoiceValuesByOrder(Order $order): float
    {
        return $order->invoiceValues()->sum('value');
    }
}
