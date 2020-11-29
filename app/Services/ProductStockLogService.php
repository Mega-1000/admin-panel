<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProductStockLogRepository;
use Carbon\Carbon;

class ProductStockLogService
{
    protected $repository;

    public function __construct(ProductStockLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function storeProductQuantityChangeLog(
        int $productStockId,
        int $productStockFirstPositionId,
        int $packetQuantity,
        string $action,
        int $userId
    ): void {
        $this->repository->create([
            'product_stock_id' => $productStockId,
            'product_stock_position_id' => $productStockFirstPositionId,
            'action' => $action,
            'quantity' => $packetQuantity,
            'user_id' => $userId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
