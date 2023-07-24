<?php

namespace App\Repositories;

use App\Entities\Order;
use FontLib\TrueType\Collection;

class OrderPackageRealCostsForCompany {
    public static function getAllByOrderId(int $orderId): float
    {
        $order = Order::find($orderId);

        $realCosts = $order->packages->map(function ($orderPackage) {
            return $orderPackage->realCostsForCompany;
        });

        $sum = 0;
        foreach($realCosts as $cost) {
            $sum += (float)$cost->first()?->cost;
        }

        return $sum;
    }

}
