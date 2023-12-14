<?php

namespace App\Repositories;

use App\Entities\Order;
use Ramsey\Collection\Collection;

class OrderPackageRealCostsForCompany {
    public static function getAllByOrderId(int|string $orderId): float
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

    /**
     * @param int $order
     * @return Collection
     */
    public static function getAllCostsByOrder(int $order): mixed
    {
        return Order::find($order)->packages->map(function ($orderPackage) {
            return $orderPackage->realCostsForCompany;
        });
    }
}
