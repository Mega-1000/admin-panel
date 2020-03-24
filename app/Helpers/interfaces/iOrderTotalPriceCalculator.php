<?php

namespace App\Helpers\interfaces;


use App\Entities\Order;

interface iOrderTotalPriceCalculator
{
    public function addItem($price, $quantity);

    public function getTotal();
}
