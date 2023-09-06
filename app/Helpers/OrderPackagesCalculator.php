<?php

namespace App\Helpers;

use App\Entities\Order;

class OrderPackagesCalculator
{
    public static function calculate(Order $order): array
    {
        $glsks = 0;
        $glskd = 0;
        $dpdd = 0;

        foreach ($order->items as $item) {
            switch ($item->product->delivery_type) {
                case 'GLS':
                    $glsks += $item->quantity / (int)$item->product->assortment_quantity;
                    break;
                case 'GLSd':
                    $glskd += $item->quantity / (int)$item->product->assortment_quantity;
                    break;
                case 'DPDd':
                    $dpdd += $item->quantity / (int)$item->product->assortment_quantity;
                    break;
            }
        }

        return [
            'GLSks' => ceil($glsks),
            'GLSkd' => ceil($glskd),
            'DPDd' => ceil($dpdd)
        ];
    }

    public static function getFullCost(Order $order): float
    {
        $data= self::calculate($order);
        return array_sum([
            $data['GLSks'] * 18,
            $data['GLSkd'] * 18,
            $data['DPDd'] * 48
        ]);
    }
}
