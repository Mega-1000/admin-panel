<?php

namespace App\Observers\Entities;

use App\Entities\OrderPayment;
use App\Helpers\OrdersRecalculatorBasedOnPeriod;
use App\Http\Controllers\RecalculateLabelsInOrdersBasedOnPeriod;
use App\Jobs\SendSpeditionNotifications;
use App\Repositories\Orders;
use App\Services\AllegroPaymentsReturnService;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use App\Services\LabelService;
use App\Services\OrderAddressService;
use App\Services\OrderPaymentLabelsService;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;

class OrderPaymentObserver
{
    public function __construct(
        protected Orders                    $orderRepository,
        protected LabelService              $labelService,
        protected OrderPaymentLabelsService $orderPaymentLabelsService,
        protected OrderService              $orderService,
    ) {}

    /**
     * @param OrderPayment $orderPayment
     *
     * @return void
     * @throws \Exception
     */
    public function created(OrderPayment $orderPayment): void
    {
        $this->addLabelIfManualCheckIsRequired($orderPayment);
        $this->orderPaymentLabelsService->calculateLabels($orderPayment->order);

        AllegroPaymentsReturnService::checkAllegroReturn($orderPayment->order);
        OrdersRecalculatorBasedOnPeriod::recalculateOrdersBasedOnPeriod($orderPayment->order);

        $sumOfPayments = $orderPayment->order->payments->where('operation_type', 'Wpłata/wypłata bankowa')->sum('amount');

        if ($sumOfPayments == $orderPayment->order->getValue()) {
            $arr = [];
            AddLabelService::addLabels($orderPayment->order, [41], $arr, [], Auth::user()?->id);
            RemoveLabelService::removeLabels($orderPayment->order, [288], $arr, [], Auth::user()?->id);
        } else {
            $arr = [];
            AddLabelService::addLabels($orderPayment->order, [288], $arr, [], Auth::user()?->id);
            RemoveLabelService::removeLabels($orderPayment->order, [41], $arr, [], Auth::user()?->id);
        }
    }

    /**
     * @param OrderPayment $orderPayment
     * @return void
     */
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
    public function deleting(OrderPayment $orderPayment): void
    {
        if ($orderPayment->rebooked_order_payment_id) {
            OrderPayment::where('rebooked_order_payment_id', $orderPayment->rebooked_order_payment_id)->delete();
        }

        $this->orderPaymentLabelsService->calculateLabels($orderPayment->order);
    }

    /**
     * @param OrderPayment $orderPayment
     * @return void
     */
    public function updated(OrderPayment $orderPayment): void
    {
        $this->orderPaymentLabelsService->calculateLabels($orderPayment->order);

        $this->orderService->calculateInvoiceReturnsLabels($orderPayment->order);

        OrdersRecalculatorBasedOnPeriod::recalculateOrdersBasedOnPeriod($orderPayment->order);
    }
}
