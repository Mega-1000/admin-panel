<?php

namespace App\Helpers;

use App\Helpers\interfaces\iOrderTotalPriceCalculator;

class SelloPriceCalculator implements iOrderTotalPriceCalculator
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
