<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\ProductStockRepository;

class ProductStockService
{
    protected $productStockRepository;
    protected $productRepository;

    public function __construct(ProductStockRepository $productStockRepository, ProductRepository $productRepository)
    {
        $this->productStockRepository = $productStockRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @return mixed
     */
    public function findProductStock(int $productStockId) {
        return $this->productStockRepository->find($productStockId);
    }

    /**
     * @return mixed
     */
    public function updateProductStockQuantity(
        int $productStockQuantity,
        int $currentPacketQuantityDifference,
        int $productStockId,
        int $sign
    ) {
        $stockQuantity = $sign ? $productStockQuantity + $currentPacketQuantityDifference : $productStockQuantity - abs($currentPacketQuantityDifference);

        return $this->productStockRepository->update([
            'quantity' => $stockQuantity,
        ], $productStockId);
    }

    public function checkProductStock(string $productId, string $productQuantity): array
    {
        $product = $this->productRepository->find($productId);
        $productStock = $this->findProductStock($product->stock->id);
        if($stockPosition = $productStock->position->first()) {
            if($stockPosition->position_quantity >= $productQuantity) {
                return ['status' => true, 'message' => __('product_stocks.message.product_add_success')];
            } else {
                return ['status' => false, 'message' => __('product_stocks.message.position_quantity_is_smaller')];
            }
        } else {
            return ['status' => false, 'message' => __('product_stocks.message.position_not_exists')];
        }
    }
}
