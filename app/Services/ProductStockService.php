<?php 

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProductStockRepository;

class ProductStockService
{
    protected $repository;

    public function __construct(ProductStockRepository $repository)
    {
        $this->repository = $repository;
    }

    public function update(int $productStockQuantity, int $currentPacketQuantityDifference, int $productStockId): void {
        $this->repository->update([
            'quantity' => $productStockQuantity + $currentPacketQuantityDifference
        ], $productStockId);
    }

}
