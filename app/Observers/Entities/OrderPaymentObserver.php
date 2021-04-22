<?php

namespace App\Observers\Entities;

use App\Entities\OrderPayment;
use App\Jobs\AddLabelJob;
use App\Services\OrderAddressService;

class OrderPaymentObserver
{
    public function created(OrderPayment $orderPayment)
    {
        $this->addLabelIfManualCheckIsRequired($orderPayment);
    }

    protected function addLabelIfManualCheckIsRequired(OrderPayment $orderPayment): void
    {
        foreach ($orderPayment->order->addresses as $orderAddress) {
            if (!(new OrderAddressService())->addressIsValid($orderAddress)) {
                dispatch_now(new AddLabelJob($orderAddress->order->id, [184]));
            }
        }
    }
}
