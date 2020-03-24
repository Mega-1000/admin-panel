<?php

namespace App\Helpers\interfaces;


use App\Entities\Order;

interface iDividable
{
    public function divide($data, Order $order);
}
