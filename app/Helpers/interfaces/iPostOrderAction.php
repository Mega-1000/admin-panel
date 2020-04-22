<?php


namespace App\Helpers\interfaces;


use App\Entities\Order;

interface iPostOrderAction
{
    public function run(Order $order);
}
