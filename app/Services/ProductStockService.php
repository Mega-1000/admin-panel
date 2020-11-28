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

    public function update(int $productStockQuantity, int $currentPacketQuantityDifference, int $productStockId): void {
        $this->repository->update([
            'quantity' => $productStockQuantity + $currentPacketQuantityDifference
        ], $productStockId);
    }

}
