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

    /**
     * get orders without reminder for label
     *
     * @param int $labelId
     * @return Collection $ordersWithoutReminder
     */
    public static function getOrdersWithoutReminderForLabel(int $labelId = 224): Collection
    {
        return Order::query()->whereHas('labels', function ($query) use ($labelId) {
            $query->where('label_id', $labelId);
        })->where('reminder_date', null)->get();
    }
}
