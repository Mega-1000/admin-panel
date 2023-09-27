<?php

namespace App\Observers\Entities;

use App\Entities\EmailSetting;
use App\OrderLabel;

class OrderLabelsObserver
{
    /**
     * Handle the OrderLabel "created" event.
     *
     * @param OrderLabel $orderLabel
     * @return void
     */
    public function created(OrderLabel $orderLabel): void
    {
        if ($orderLabel->id = 50) {
            EmailSetting::find(36)->sendEmail($orderLabel->order->customer->login);
        }
    }
}
