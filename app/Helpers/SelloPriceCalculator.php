<?php

namespace App\Helpers;

class SelloPriceCalculator
{
    private $total = 0;
    private $overridePrice;

    public function addItem($price, $quantity)
    {
        $this->total += $this->overridePrice * $quantity;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setOverridePrice($overridePrice)
    {
        $this->overridePrice = $overridePrice;
    }
}
