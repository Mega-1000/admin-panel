<?php

namespace App\Repositories;

use App\Entities\Firm;
use App\Entities\Product;
use App\Entities\Warehouse;
use Illuminate\Database\Eloquent\Collection;
class Firms
{
    /**
     * Get all products for firm
     *
     * @param String $warehouseSymbol
     * @return Collection
     */
    public static function getAllProductsForFirm(string $warehouseSymbol): Collection
    {
        return Product::where('manufacturer', $warehouseSymbol)->with('packing')->get();
    }

    /**
     * Get firm by warehouse symbol
     *
     * @param string $warehouseSymbol
     * @return Firm
     */
    public static function getFirmByWarehouseSymbol(string $warehouseSymbol): Firm
    {
        return Warehouse::where('symbol', $warehouseSymbol)->first()->firm;
    }

    /**
     * Get firm by symbol
     *
     * @param String $firmSymbol
     * @return Firm
     */
    public static function getFirmBySymbol(string $firmSymbol): Firm
    {
        return Firm::where('symbol', $firmSymbol)->first();
    }
}
