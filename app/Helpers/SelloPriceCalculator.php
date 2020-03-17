<?php

namespace App\Helpers;

class SelloPriceCalculator
{
    private $total = 0;

    public function addItem($price, $quantity)
    {
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setOverridePrice($overridePrice)
    {
        $this->total = $overridePrice;
    }
}
