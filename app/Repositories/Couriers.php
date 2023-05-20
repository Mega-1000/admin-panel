<?php declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Courier;
use Illuminate\Database\Eloquent\Collection;

class Couriers
{
    /**
     * Get courier orderBy
     * @return Collection<Courier>
     */
    public static function getOrderByNumber(): Collection
    {
        return Courier::query()->orderBy('item_number')->get();
    }

    /**
     * Get courier orderBy
     * @return Collection<Courier>
     */
    public static function getActiveOrderByNumber(): Collection
    {
        return Courier::query()->where('active', 1)->orderBy('item_number')->get();
    }
}
