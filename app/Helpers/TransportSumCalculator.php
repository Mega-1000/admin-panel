<?php


namespace App\Helpers;


use App\Helpers\interfaces\iSumable;

class TransportSumCalculator implements iSumable
{

    public function __construct()
    {
    }

    public function getSum($order)
    {
        return $order->getTransportPrice();
    }
}
