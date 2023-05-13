<?php

namespace App\Observers\Entities;

use App\Entities\OrderPayment;
use App\Repositories\OrderPayments;
use App\Services\Label\AddLabelService;
use App\Services\OrderAddressService;
use Illuminate\Support\Facades\Auth;

class OrderPaymentObserver
{
    public function created(OrderPayment $orderPayment): void
    {
        $this->addLabelIfManualCheckIsRequired($orderPayment);
    }

    protected function addLabelIfManualCheckIsRequired(OrderPayment $orderPayment): void
    {
        foreach ($orderPayment->order->addresses as $orderAddress) {
            if (!(new OrderAddressService())->addressIsValid($orderAddress)) {
                $loopPresentationArray = [];
                AddLabelService::addLabels($orderPayment->order, [184], $loopPresentationArray, [], Auth::user()?->id);
            }
        }
    }

    /**
     * @param OrderPayment $orderPayment
     *
     * @return void
     */
    public function deleted(OrderPayment $orderPayment): void
    {
        if($orderPayment->rebooked_order_payment_id) {
            OrderPayment::where('rebooked_order_payment_id', $orderPayment->rebooked_order_payment_id)->delete();
        }
    }
}
