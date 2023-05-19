<?php

namespace App\DTO\Task;

use App\DTO\BaseDTO;
use JsonSerializable;

class StockListDTO extends BaseDTO implements JsonSerializable
{
    public function __construct(
        private int $product_stock_id,
        private string $product_name,
        private string $product_symbol,
        private int $quantity,
        private int $stock_quantity,
        private int $first_position_quantity
    )
    {
        $this->product_stock_id = $product_stock_id;
        $this->product_name = $product_name;
        $this->product_symbol = $product_symbol;
        $this->quantity = $quantity;
        $this->stock_quantity = $stock_quantity;
        $this->first_position_quantity = $first_position_quantity;
    }

    public function jsonSerialize()
    {
        return [
            'product_stock_id' => $this->product_stock_id,
            'product_name' => $this->product_name,
            'product_symbol' => $this->product_symbol,
            'quantity' => $this->quantity,
            'stock_quantity' => $this->stock_quantity,
            'first_position_quantity' => $this->first_position_quantity
        ];
    }
}