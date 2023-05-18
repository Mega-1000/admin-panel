<?php

namespace App\Helpers;

use App\Entities\Courier;
use Illuminate\Support\Collection;

class CourierHelper
{
    /**
     * Get courier orderBy
     * @return Collection<Courier>
     */
    public static function getOrderByNumber(): Collection
    {
        return Courier::orderBy('item_number')->get();
    }

    /**
     * Get courier orderBy
     * @return Collection<Courier>
     */
    public static function getActiveOrderByNumber(): Collection
    {
        return Courier::where('active',1)->orderBy('item_number')->get();
    }
}
