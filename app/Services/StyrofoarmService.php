<?php

namespace App\Services;

use App\Entities\PostalCodeLatLon;
use App\Exceptions\DeliverAddressNotFoundException;
use App\Repositories\PostalCodeLatLons;
use App\Repositories\Products;
use Exception;
use Illuminate\Support\Facades\DB;

class StyrofoarmService
{
    public function __construct(
        private readonly ProductService $productService
    )
    {
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getStyrofoarmsByFirms(): array
    {
//        $products = Products::getStyrofoarmsWithoutVariations();
//        $firms = [];
//        $response = [];
//        //output should be like this:
//        // Product1: [product_from_firm1, product_from_firm2, product_from_firm3],
//        // Product2: [product_from_firm1, product_from_firm2, product_from_firm3]
//
//        $variations = $this->productService->getVariationsFromProducts($products, PostalCodeLatLons::getLatLonByPostalCode('66-400'));
//
//        foreach ($firms as $product => $firm) {
//            $response[$product] = array_unique($firm);
//        }
//
//        return $firms;
    }
}
