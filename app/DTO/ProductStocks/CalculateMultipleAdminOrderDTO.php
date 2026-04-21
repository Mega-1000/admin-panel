<?php

namespace App\DTO\ProductStocks;

use App\Entities\ProductStock;

readonly class CalculateMultipleAdminOrderDTO
{
    public function __construct(
        public ProductStock $productStock,
        public int $daysBack,
        public int $daysToFuture
    ) {
    }

    public static function fromRequest(ProductStock $productStock, array $data): self
    {
        return new self(
            $productStock,
            $data['daysBack'],
            $data['daysToFuture']
        );
    }
}
