<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProductStockRepository;

class ProductStockService
{
    protected $productStockRepository;

    public function __construct(ProductStockRepository $productStockRepository)
    {
        $this->productStockRepository = $productStockRepository;
    }

    public function findProductStock(int $productStockId) {
        return $this->productStockRepository->find($productStockId);
    }

    /**
     * @return mixed
     */
    public function updateProductStockQuantity(
        int $productStockQuantity,
        int $currentPacketQuantityDifference,
        int $productStockId
    ) {
        return $this->productStockRepository->update([
            'quantity' => $productStockQuantity + $currentPacketQuantityDifference,
        ], $productStockId);
    }
}
