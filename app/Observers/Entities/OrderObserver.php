<?php

namespace App\Observers\Entities;

use App\Entities\Order;
use App\Entities\Status;
use App\Facades\Mailer;
use App\Helpers\OrderPackagesCalculator;
use App\Jobs\calculateLabelsForOrder;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Jobs\FireProductPacketJob;
use App\Mail\ShipmentDateInOrderChangedMail;
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
    public function __construct(
        protected StatusRepository             $statusRepository,
        protected OrderPaymentLabelsService    $orderPaymentLabelsService,
        protected OrderService                 $orderService,
        protected OrderPackagesCalculator      $orderPackagesCalculator,
    ) {}

    /**
     * @throws Exception
     */
    public function created(Order $order): void
    {
        $this->orderPaymentLabelsService->calculateLabels($order);

        $order->token = Str::random(32);
        $order->save();

        dispatch(new FireProductPacketJob($order));
        dispatch(new calculateLabelsForOrder($order));
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
        if (count($order->payments)) {
            if ($order->isPaymentRegulated()) {
                dispatch(new DispatchLabelEventByNameJob($order, "payment-equal-to-order-value"));
            } else {
                dispatch(new DispatchLabelEventByNameJob($order, "required-payment-before-unloading"));
            }
        }

        $hasMissingDeliveryAddressLabel = $order->labels()->where('label_id', 75)->get();

        if (count($hasMissingDeliveryAddressLabel) > 0) {
            if ($order->isDeliveryDataComplete()) {
                dispatch(new DispatchLabelEventByNameJob($order, "added-delivery-address"));
            }
        }

        $this->orderPaymentLabelsService->calculateLabels($order);
        $arr = [];

        $additional_service = $order->additional_service_cost ?? 0;
        $additional_cod_cost = $order->additional_cash_on_delivery_cost ?? 0;
        $shipment_price_client = $order->shipment_price_for_client ?? 0;
        $totalProductPrice = 0;

        foreach ($order->items as $item) {
            $price = $item->gross_selling_price_commercial_unit ?: $item->net_selling_price_commercial_unit ?: 0;
            $quantity = $item->quantity ?? 0;
            $totalProductPrice += $price * $quantity;
        }

        $depositPaidData = $this->orderDepositPaidCalculator->calculateDepositPaidOrderData($order);

        $sumOfGrossValues = $totalProductPrice + $additional_service + $additional_cod_cost + $shipment_price_client;

        if (
            round($sumOfGrossValues) + round($depositPaidData['returnedValue']) - round($depositPaidData['balance']) - round($depositPaidData['wtonValue']) == 0.0 &&
            $order->payments->count() > 0
        ) {
            $order = Order::find($order->id);
            $LpArray = [];
//            RemoveLabelService::removeLabels($order, [39], $LpArray, [], Auth::user()->id);
        } else {
            AddLabelService::addLabels($order, [39], $arr, [], Auth::user()?->id);
        }

    }

    public function labelsAttached(Order $order): void
    {
        dd('okej');
        if ($order->labels->contains(50)) {
            dd('parabole ja pierdole');
        }
    }

}
