<?php

namespace App\Helpers\interfaces;


use App\Entities\Order;

interface iOrderTotalPriceCalculator
{

    public function getTotal();
}
