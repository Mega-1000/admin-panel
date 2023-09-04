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
            $item = $item->product;
            switch ($item->delivery_type) {
                case 'GLS':
                    $glsks += $item->amount / $item->assortment_quantity;
                    break;
                case 'GLSd':
                    $glskd += $item->amount / $item->assortment_quantity;
                    break;
                case 'DPDd':
                    $dpdd += $item->amount / $item->assortment_quantity;
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
