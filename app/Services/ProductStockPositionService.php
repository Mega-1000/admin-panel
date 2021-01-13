<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProductStockPositionRepository;

class ProductStockPositionService
{
    protected $productStockPositionRepository;

    public function __construct(ProductStockPositionRepository $productStockPositionRepository)
    {
        $this->productStockPositionRepository = $productStockPositionRepository;
    }

    /**
     * @return mixed
     */
    public function updateProductPositionQuantity(
        int $positionQuantity,
        int $packetQuantity,
        int $productStockPositionId,
        int $sign
    ) {
        $positionQuantity = $sign ? $positionQuantity + $packetQuantity : $positionQuantity - $packetQuantity;

        return $this->productStockPositionRepository->update([
            'position_quantity' => $positionQuantity,
        ], $productStockPositionId);
    }

}
