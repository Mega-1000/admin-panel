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
        $key = $orderItem->product_id;
        if ($orderItem->type == 'multiple') {
            $key = $orderItem->product_id . '_' . $orderItem->quantity;
        }
        if (empty($this->overrides[$key])) {
            return $orderItem;
        }
        $prices = $this->overrides[$key];
        foreach (OrderBuilder::getPriceColumns() as $column) {
            if (isset($prices[$column])) {
                $orderItem->$column = $prices[$column];
            }
        }
        return $orderItem;
    }
}
