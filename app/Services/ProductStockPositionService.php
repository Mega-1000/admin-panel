<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\ProductStock;
use App\Entities\ProductStockPosition;

class ProductStockPositionService
{
    /**
     * @param int $positionQuantity
     * @param int $packetQuantity
     * @param int $productStockPositionId
     * @param int $sign
     * @return mixed
     */
    public function updateProductPositionQuantity(
        int $positionQuantity,
        int $packetQuantity,
        int $productStockPositionId,
        int $sign
    ): mixed
    {
        $positionQuantity = $sign
            ? $positionQuantity + $packetQuantity :
            $positionQuantity - abs($packetQuantity);

        return ProductStockPosition::find($productStockPositionId)->update([
            'position_quantity' => $positionQuantity,
        ]);
    }

    /**
     * @param ProductStock $productStock
     * @return void
     */
    public static function calculateQuantityForProductStock(ProductStock $productStock): void
    {
        $productStock->update([
            'quantity',
            $productStock->position->sum('position_quantity'),
        ]);
    }

}
