<?php

namespace App\Repositories;

use App\Entities\Order;

class OrderPackageRealCostsForCompany {
    public static function getAllByOrderId(int $orderId): float
    {
        $order = Order::find($orderId);

        $realCosts = $order->packages->map(function ($orderPackage) {
            return $orderPackage->realCostsForCompany;
        });

        $sum = 0;
        foreach($realCosts as $cost) {
            $cost->each(function ($cost) use (&$sum) {
                $sum += $cost->cost;
            });
        }

        return $sum;
    }

}
