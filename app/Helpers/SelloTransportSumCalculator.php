<?php


namespace App\Helpers;


use App\Helpers\interfaces\iSumable;

class SelloTransportSumCalculator implements iSumable
{
    private float $transportPrice;

    public function getSum($order): float
    {
        return $this->transportPrice;
    }

    public function setTransportPrice($transportPrice): void
    {
        $this->transportPrice = $transportPrice;
    }
}
