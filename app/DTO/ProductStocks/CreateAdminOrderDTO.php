<?php

namespace App\DTO\ProductStocks\ProductStocks;

use App\Entities\ProductStock;

readonly class CreateAdminOrderDTO
{
    public function __construct(
        public int $daysBack,
        public int $daysToFuture,
        public string $clientEmail,
        public ProductStock $productStock
    ) {
    }

    public static function fromRequest(array $data, ProductStock $productStock): self
    {
        return new self(
            $data['daysBack'],
            $data['daysToFuture'],
            $data['clientEmail'],
            $productStock
        );
    }

}
