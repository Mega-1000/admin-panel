<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\ProductStockPosition;
use App\Repositories\ProductStockPositionRepository;

class ProductStockPositionService
{
    protected $productStockPositionRepository;

    public function __construct(ProductStockPositionRepository $productStockPositionRepository)
    {
        $this->productStockPositionRepository = $productStockPositionRepository;
    }

    public function updateProductPositionQuantity(int $positionQuantity, int $packetQuantity, int $productStockPositionId, int $sign): void {
        $positionQuantity = $sign ? $positionQuantity - $packetQuantity : $positionQuantity + $packetQuantity;

        $this->productStockPositionRepository->update([
            'position_quantity' => $positionQuantity
        ], $productStockPositionId);
    }

}
