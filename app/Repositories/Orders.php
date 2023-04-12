<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use App\Entities\Order;

class Orders
{
    /**
     * get chat orders (disputes) need support
     *
     * @return Collection|null $ordersNeedSupport
     */
    public static function getChatOrdersNeedSupport(): ?Collection
    {
        $ordersNeedSupport = Order::where('need_support', true)->whereHas('chat', function($q) {
            $q->whereNull('user_id');
        })->get();

        return $ordersNeedSupport;
    }


    public static function getOrdersWithoutReminderForLabel(int $labelId): Collection
    {
        return Order::query()->whereHas('labels', function ($query) {
            $query->where('label_id', 224);
        })->where('reminder_date', null)->get();
    }
}
