<?php


namespace App\Helpers;


use App\Helpers\interfaces\iSumable;

class SelloTransportSumCalculator implements iSumable
{

    private $transportPrice;

    public function __construct()
    {
    }

    public function getSum($order)
    {
        return $this->transportPrice;
    }

    public function setTransportPrice($transportPrice): void
    {
        $this->transportPrice = $transportPrice;
    }
}
