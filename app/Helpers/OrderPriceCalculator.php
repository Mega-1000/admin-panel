<?php

namespace App\Helpers;


class OrderPriceCalculator
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
