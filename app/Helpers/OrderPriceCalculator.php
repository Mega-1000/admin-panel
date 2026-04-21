<?php

namespace App\Helpers;


use App\Helpers\interfaces\iOrderTotalPriceCalculator;

class OrderPriceCalculator implements iOrderTotalPriceCalculator
{
    private int $total = 0;

    public function addItem($price, $quantity)
    {
        $this->total += $price * $quantity;
    }

    public function getTotal(): int
    {
        return $this->total;
    }
}
