<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\ProductSymbolCoreExtractor;
use App\Repositories\ProductRepository;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @return mixed
     */
    public function checkForSimilarProducts(int $productId)
    {
        $product = $this->productRepository->find($productId);
        $productSymbolCore = ProductSymbolCoreExtractor::getProductSymbolCore($product->symbol);

        return $this->productRepository->findWhere([
            ['symbol', 'LIKE', '%' . $productSymbolCore . '%']
        ]);
    }

    /**
     * @return mixed
     */
    public function getStockProduct(int $productId)
    {
        $similarProducts = $this->checkForSimilarProducts($productId);

        return $similarProducts->first(function ($similarProduct) {
            return $similarProduct->stock_product == true;
        });
    }
}
