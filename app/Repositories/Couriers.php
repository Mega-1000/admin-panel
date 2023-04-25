<?php

declare(strict_types=1);

namespace App\Repositories;


use App\Entities\Courier;
use Illuminate\Database\Eloquent\Collection;

class Couriers
{
    /**
     * Get courier orderBy
     */
    public static function getOrderByNumber(): Collection
    {
        return Courier::orderBy('item_number')->get();
    }

    /**
     * Get courier orderBy
     */
    public static function getActiveOrderByNumber(): Collection
    {
        return Courier::where('active',1)->orderBy('item_number')->get();
    }
}
