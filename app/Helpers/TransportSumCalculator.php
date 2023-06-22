<?php


namespace App\Helpers;


use App\Helpers\interfaces\iSumable;

class TransportSumCalculator implements iSumable
{
    public function getSum($order)
    {
        return $order->getTransportPrice();
    }
}
