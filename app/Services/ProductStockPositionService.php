<?php 

declare(strict_types=1);

namespace App\Services;

use App\Entities\ProductStockPosition;
use App\Repositories\ProductStockPositionRepository;

class ProductStockPositionService
{
    protected $repository;

    public function __construct(ProductStockPositionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function update($positionQuantity, $packetQuantity, $productStockPositionId, int $sign): void {
        if($sign === 0) {
            $positionQuantity = $positionQuantity - $packetQuantity;
        } else {
            $positionQuantity = $positionQuantity + $packetQuantity;
        }

        $this->repository->update([
            'position_quantity' => $positionQuantity
        ], $productStockPositionId);
    }

}
