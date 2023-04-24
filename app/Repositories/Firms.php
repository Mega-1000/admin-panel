<?php

namespace App\Repositories;

use App\Entities\Firm;
use App\Entities\Product;
use Illuminate\Database\Eloquent\Collection;
class Firms
{
    /**
     * Get all products for firm
     *
     * @param String $firmSymbol
     * @return Collection
     */
    public static function getAllProductsForFirm(String $firmSymbol): Collection
    {
        return Product::where('manufacturer', $firmSymbol)->with('packing')->get();
    }

    public static function getFirmBySymbol(String $firmSymbol): Firm
    {
        return Firm::where('symbol', $firmSymbol)->first();
    }
}
