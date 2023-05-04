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
        $products = Products::getStyrofoarmsWithoutVariations();
    }
}
