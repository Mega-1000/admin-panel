<?php


namespace App\Helpers;


use App\Entities\OrderItem;
use App\Helpers\interfaces\iOrderPriceOverrider;

class OrderPriceOverrider implements iOrderPriceOverrider
{
    private $overrides;

    public function __construct($overrides)
    {
        $this->overrides = $overrides;
    }

    public function override(OrderItem $orderItem)
    {
        if (empty($this->overrides[$orderItem->product_id])) {
            return $orderItem;
        }
        $prices = $this->overrides[$orderItem->product_id];
        foreach ($prices as $k => $price) {
            if (strpos($k, 'gross') !== false) {
                $tax = 1 + env('VAT');
                $newKey = str_replace('gross', 'net', $k);
                $prices[$newKey] = $price / $tax;
            }
        }
        foreach (OrderBuilder::getPriceColumns() as $column) {
            if (isset($prices[$column])) {
                $orderItem->$column = $prices[$column];
            }
        }
        return $orderItem;
    }
}
