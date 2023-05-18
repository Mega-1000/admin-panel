<?php

namespace App\Helpers;

use App\Entities\Courier;

class CourierHelper
{
    /**
     * Get courier orderBy
     */
    public static function getOrderByNumber()
    {
        return Courier::orderBy('item_number')->get();
    }

    /**
     * Get courier orderBy
     */
    public static function getActiveOrderByNumber()
    {
        return Courier::where('active',1)->orderBy('item_number')->get();
    }
}
