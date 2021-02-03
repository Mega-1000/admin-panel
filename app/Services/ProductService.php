<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\Product;
use App\Helpers\ProductSymbolCoreExtractor;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function checkForSimilarProducts(int $productId): ?Collection
    {
        $product = $this->productRepository->find($productId);
        $productSymbolCore = ProductSymbolCoreExtractor::getProductSymbolCore($product->symbol);

        return $this->productRepository->findWhere([
            ['symbol', 'LIKE', '%' . $productSymbolCore . '%']
        ]);
    }

    public function getStockProduct(int $productId): ?Product
    {
        $similarProducts = $this->checkForSimilarProducts($productId);

        return $similarProducts->first(function ($similarProduct) {
            return $similarProduct->stock_product === true;
        });
    }
}
