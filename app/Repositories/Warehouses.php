<?php

namespace App\Repositories;

use App\Entities\Warehouse;

class Warehouses
{
    /**
     * @return array<string>
     */
    public static function getAllWarehousesSymbols(): array
    {
        return Warehouse::distinct('symbol')->pluck('symbol')->toArray();
    }

    public static function getIdFromSymbol(string $getWarehouseSymbol)
    {
        return Warehouse::where('symbol', $getWarehouseSymbol)->first()->id;
    }
}
