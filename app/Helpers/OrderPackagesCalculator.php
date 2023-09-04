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
                    $glsks += $item->amount / $item->product->assortment_quantity;
                    break;
                case 'GLSd':
                    $glskd += $item->amount / $item->product->assortment_quantity;
                    break;
                case 'DPDd':
                    $dpdd += $item->amount / $item->product->assortment_quantity;
                    break;
            }
        }

        return [
            'GLSks' => ceil($glsks),
            'GLSkd' => ceil($glskd),
            'DPDd' => ceil($dpdd)
        ];
    }
}
