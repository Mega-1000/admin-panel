<?php

namespace App\Observers;

use App\Entities\OrderPackage;
use Illuminate\Support\Str;

class OrderPackageObserver
{
    /**
     * Handle the OrderPackage "created" event.
     *
     * @param OrderPackage $orderPackage
     * @return void
     */
    public function created(OrderPackage $orderPackage): void
    {
        // is cash on delivery
        if ($orderPackage->cash_on_delivery > 0) {
            $orderPackage->orderPayments()->create([
                'declared_sum' => $orderPackage->cash_on_delivery,
                'type' => 'cash_on_delivery',
                'status' => 'new',
                'token' => Str::random(32),
                'order_id' => $orderPackage->order_id,
                'tracking_number' => $orderPackage->tracking_number,
            ]);
        }
    }

    /**
     * Handle the OrderPackage "updated" event.
     *
     * @param OrderPackage $orderPackage
     * @return void
     */
    public function updated(OrderPackage $orderPackage): void
    {
        if ($orderPackage->cash_on_delivery) {
            $orderPackage->orderPayments()->update([
                'declared_sum' => $orderPackage->cash_on_delivery,
            ]);
        }
    }

    /**
     * Handle the OrderPackage "deleted" event.
     *
     * @param OrderPackage $orderPackage
     * @return void
     */
    public function deleted(OrderPackage $orderPackage): void
    {
        $orderPackage->orderPayments()->delete();
    }
}
