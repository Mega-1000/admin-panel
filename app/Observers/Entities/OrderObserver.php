<?php

namespace App\Observers\Entities;

use App\Entities\Order;
use App\Entities\Status;
use App\Facades\Mailer;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Mail\ShipmentDateInOrderChangedMail;
use App\Repositories\Orders;
use App\Repositories\StatusRepository;
use App\Services\Label\AddLabelService;
use App\Services\OrderPaymentLabelsService;
use App\Services\OrderService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

readonly class OrderObserver
{
    /**
     * OrderObserver constructor.
     * @param StatusRepository $statusRepository
     * @param OrderPaymentLabelsService $orderPaymentLabelsService
     * @param OrderService $orderService
     */
    public function __construct(
        protected StatusRepository          $statusRepository,
        protected OrderPaymentLabelsService $orderPaymentLabelsService,
        protected OrderService              $orderService,
    ) {}

    public function created(Order $order): void
    {
        dispatch(new DispatchLabelEventByNameJob($order, "new-order-created"));

        $this->orderPaymentLabelsService->calculateLabels($order);

        $order->token = Str::random(32);
        $order->save();
    }

    public function updating(Order $order): void
    {
        if (!$order->isDirty()) {
            return;
        }
        if (!empty($order->getDirty()['status_id'])) {
            $statusId = $order->getDirty()['status_id'];
            /** @var Status $status */
            $status = Status::query()->find($statusId);
            $loopPresentationArray = [];
            AddLabelService::addLabels($order, $status->labelsToAddOnChange()->pluck('labels.id')->toArray(), $loopPresentationArray, [], Auth::user()?->id);
        }

        if (!empty($order->getDirty()['employee_id'])) {
            dispatch(new DispatchLabelEventByNameJob($order, "consultant-changed"));
        }

        if (!empty($order->getDirty()['shipment_date'])) {
            $original = $order->getOriginal('shipment_date');
            $newDate = $order->shipment_date;

            if ((new Carbon($original))->diffInDays($newDate) !== 0) {
                try {
                    if (strpos($order->customer->login, 'allegromail.pl')) {
                        return;
                    }
                    Mailer::create()
                        ->to($order->customer->login)
                        ->send(new ShipmentDateInOrderChangedMail([
                            'oldDate' => $original,
                            'newDate' => $newDate,
                            'orderId' => $order->id,
                        ]));
                } catch (Exception $exception) {
                    Log::error('Can\'t send email about shipment date change .',
                        ['exception' => $exception->getMessage(), 'class' => $exception->getFile(), 'line' => $exception->getLine()]
                    );
                }
            }
        }
    }

    public function updated(Order $order): void
    {
        $this->orderService->calculateInvoiceReturnsLabels($order);

        if (count($order->payments)) {
            if ($order->isPaymentRegulated()) {
                dispatch(new DispatchLabelEventByNameJob($order, "payment-equal-to-order-value"));
            } else {
                dispatch(new DispatchLabelEventByNameJob($order, "required-payment-before-unloading"));
            }
        }

        $hasMissingDeliveryAddressLabel = $order->labels()->where('label_id', 75)->get();    //brak danych do dostawy

        if (count($hasMissingDeliveryAddressLabel) > 0) {
            if ($order->isDeliveryDataComplete()) {
                dispatch(new DispatchLabelEventByNameJob($order, "added-delivery-address"));
            }
        }

        $this->orderPaymentLabelsService->calculateLabels($order);
    }
}
