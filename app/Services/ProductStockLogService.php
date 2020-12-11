<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProductStockLogRepository;
use Carbon\Carbon;

class ProductStockLogService
{
    protected $productStockLogRepository;

    public function __construct(ProductStockLogRepository $productStockLogRepository)
    {
        $this->productStockLogRepository = $productStockLogRepository;
    }

    public function storeProductQuantityChangeLog(
        int $productStockId,
        int $productStockFirstPositionId,
        int $packetQuantity,
        string $action,
        int $userId
    ) {
        return $this->productStockLogRepository->create([
            'product_stock_id' => $productStockId,
            'product_stock_position_id' => $productStockFirstPositionId,
            'action' => $action,
            'quantity' => $packetQuantity,
            'user_id' => $userId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
