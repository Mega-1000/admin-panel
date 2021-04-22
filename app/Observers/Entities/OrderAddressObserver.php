<?php

namespace App\Observers\Entities;

use App\Entities\OrderAddress;
use App\Jobs\AddLabelJob;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Jobs\RemoveLabelJob;
use App\Services\OrderAddressService;
use App\Services\OrderPaymentService;

class OrderAddressObserver
{
    public function created(OrderAddress $orderAddress)
    {
        $this->removingMissingDeliveryAddressLabelHandler($orderAddress);
    }

    public function updated(OrderAddress $orderAddress)
    {
        $this->removingMissingDeliveryAddressLabelHandler($orderAddress);
        $this->addLabelIfManualCheckIsRequired($orderAddress);
    }

    protected function removingMissingDeliveryAddressLabelHandler(OrderAddress $orderAddress)
    {
        $hasMissingDeliveryAddressLabel = $orderAddress->order->labels()->where('label_id', 75)->get();    //brak danych do dostawy
        if (count($hasMissingDeliveryAddressLabel) > 0) {
            if ($orderAddress->type == "DELIVERY_ADDRESS") {
                if ($orderAddress->order->isDeliveryDataComplete()) {
                    dispatch_now(new DispatchLabelEventByNameJob($orderAddress->order, "added-delivery-address"));
                }
            }
        }
    }

    protected function addLabelIfManualCheckIsRequired(OrderAddress $orderAddress): void
    {
        if (app(OrderPaymentService::class)->hasAnyPayment($orderAddress->order) &&
            !(new OrderAddressService())->addressIsValid($orderAddress)) {
            dispatch_now(new AddLabelJob($orderAddress->order->id, [184]));
        } else {
            dispatch_now(new RemoveLabelJob($orderAddress->order->id, [184]));
        }
    }
}
