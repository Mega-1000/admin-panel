<?php

namespace App\Helpers\interfaces;


use App\Entities\Order;
use App\Entities\OrderItem;

interface iOrderPriceOverrider
{
    public function override(OrderItem $orderItem);
}
