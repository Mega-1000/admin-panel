<?php

namespace App\Observers\Entities;

use App\Entities\Order;
use App\Jobs\AddLabelJob;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Mail\ShipmentDateInOrderChangedMail;
use App\Repositories\StatusRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /** @var StatusRepository */
    protected $statusRepository;

    /**
     * OrderObserver constructor.
     * @param StatusRepository $statusRepository
     */
    public function __construct(StatusRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    public function created(Order $order)
    {
        dispatch_now(new DispatchLabelEventByNameJob($order->id, "new-order-created"));
    }

    public function updating(Order $order)
    {
        if (!$order->isDirty()) {
            return;
        }
        if (!empty($order->getDirty()['status_id'])) {
            $statusId = $order->getDirty()['status_id'];
            $status = $this->statusRepository->find($statusId);
            dispatch_now(new AddLabelJob($order, $status->labelsToAddOnChange));
        }

        if (!empty($order->getDirty()['employee_id'])) {
            dispatch_now(new DispatchLabelEventByNameJob($order->id, "consultant-changed"));
        }

        if (!empty($order->getDirty()['shipment_date'])) {
            $original = $order->getOriginal('shipment_date');
            $newDate = $order->shipment_date;

            if ((new Carbon($original))->diffInDays($newDate) !== 0) {
                try {
                    \Mailer::create()
                        ->to($order->customer->login)
                        ->send(new ShipmentDateInOrderChangedMail([
                            'oldDate' => $original,
                            'newDate' => $newDate,
                            'orderId' => $order->id,
                        ]));
                } catch (\Exception $exception) {
                    Log::error('Can\'t send email about shipment date change .',
                        ['exception' => $exception->getMessage(), 'class' => $exception->getFile(), 'line' => $exception->getLine()]
                    );
                }
            }
        }
    }

    public function updated(Order $order)
    {
        if (count($order->payments)) {
            if ($order->isPaymentRegulated()) {
                dispatch_now(new DispatchLabelEventByNameJob($order->id, "payment-equal-to-order-value"));
            } else {
                dispatch_now(new DispatchLabelEventByNameJob($order->id, "required-payment-before-unloading"));
            }
        }

        $hasMissingDeliveryAddressLabel = $order->labels()->where('label_id', 75)->get();    //brak danych do dostawy

        if (count($hasMissingDeliveryAddressLabel) > 0) {
            if ($order->isDeliveryDataComplete()) {
                dispatch_now(new DispatchLabelEventByNameJob($order, "added-delivery-address"));
            }
        }
    }
}
