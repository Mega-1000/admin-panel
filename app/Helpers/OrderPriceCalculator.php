<?php

namespace App\Helpers;


use App\Helpers\interfaces\iOrderTotalPriceCalculator;

class OrderPriceCalculator implements iOrderTotalPriceCalculator
{
    private $total = 0;

    public function addItem($price, $quantity)
    {
        $this->total += $price * $quantity;
    }

    public function getTotal()
    {
        return $this->total;
    }
}
