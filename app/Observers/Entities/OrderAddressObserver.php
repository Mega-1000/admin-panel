<?php

namespace App\Observers\Entities;

use App\Entities\OrderAddress;
use App\Jobs\DispatchLabelEventByNameJob;

class OrderAddressObserver
{
    public function created(OrderAddress $orderAddress)
    {
        $this->removingMissingDeliveryAddressLabelHandler($orderAddress);
    }

    public function updated(OrderAddress $orderAddress)
    {
        $this->removingMissingDeliveryAddressLabelHandler($orderAddress);
    }

    protected function removingMissingDeliveryAddressLabelHandler(OrderAddress $orderAddress)
    {
        $hasMissingDeliveryAddressLabel = $orderAddress->order->labels()->where('label_id', 75)->get();    //brak danych do dostawy
        if (count($hasMissingDeliveryAddressLabel) > 0) {
            if($orderAddress->type == "DELIVERY_ADDRESS") {
                if($orderAddress->order->isDeliveryDataComplete()) {
                    dispatch_now(new DispatchLabelEventByNameJob($orderAddress->order, "added-delivery-address"));
                }
            }
        }
    }
}
