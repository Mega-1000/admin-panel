<?php

namespace App\DTO\Task;

use App\DTO\BaseDTO;
use JsonSerializable;

class StockListDTO extends BaseDTO implements JsonSerializable
{
    public function __construct(
        private readonly int    $product_stock_id,
        private readonly string $product_name,
        private readonly string $product_symbol,
        private readonly int    $quantity,
        private readonly int    $stock_quantity,
        private readonly int    $first_position_quantity
    )
    {
    }

    public function jsonSerialize(): array
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
