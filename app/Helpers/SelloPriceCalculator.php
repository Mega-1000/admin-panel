<?php

namespace App\Helpers;

use App\Helpers\interfaces\iOrderTotalPriceCalculator;

class SelloPriceCalculator implements iOrderTotalPriceCalculator
{
    private $total = 0;
    private $products;

    public function addItem($price, $quantity)
    {
        //stub
    }

    public function getTotal()
    {
        return $this->products->reduce(function ($prev, $next) {
           $prev += $next->total_price;
           return $prev;
        },0);
    }

    public function setProductList($products)
    {
        $this->products = $products;
    }
}
