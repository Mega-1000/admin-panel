<?php

namespace App\Factory;

use App\Helpers\BackPackPackageDivider;
use App\Helpers\OrderBuilder;
use App\Helpers\OrderPriceCalculator;
use App\Helpers\OrderPriceOverrider;
use App\Services\ProductService;

class OrderBuilderFactory
{
    public static function create(): OrderBuilder
    {
        return (new OrderBuilder())
            ->setPackageGenerator(new BackPackPackageDivider())
            ->setPriceCalculator(new OrderPriceCalculator())
            ->setProductService(new ProductService())
            ->setPriceOverrider(new OrderPriceOverrider([
                'gross_selling_price_commercial_unit' => 0,
            ]));
    }
}
