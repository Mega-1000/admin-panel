<?php

namespace App\Observers\Entities;

use App\Entities\Order;
use App\Entities\OrderPayment;
use App\Repositories\OrderPayments;
use App\Repositories\Orders;
use App\Services\Label\AddLabelService;
use App\Services\LabelService;
use App\Services\OrderAddressService;
use Illuminate\Support\Facades\Auth;

class OrderPaymentObserver
{
    public function __construct(
        protected Orders $orderRepository,
        protected LabelService $labelService,
    ) {
    }

    public function created(OrderPayment $orderPayment): void
    {
        $this->addLabelIfManualCheckIsRequired($orderPayment);
        $this->calculateLabels($orderPayment->order);
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

        $this->calculateLabels($orderPayment->order);
    }

    public function updated(OrderPayment $orderPayment): void
    {
        $this->calculateLabels($orderPayment->order);
    }


    /**
     * @param Order $order
     *
     * @return void
     */
    private function calculateLabels(Order $order): void
    {
        $relatedPaymentsValue = $this->orderRepository->getAllRelatedOrderPaymentsValue($order);
        $relatedOrdersValue = $this->orderRepository->getAllRelatedOrdersValue($order);
        $arr = [];

        if ($relatedPaymentsValue === $relatedOrdersValue) {
            $this->labelService->removeLabel($order->id, [134]);
            AddLabelService::addLabels($order, [133], $arr, [], Auth::user()?->id);

            return;
        }

        $this->labelService->removeLabel($order->id, [133]);
        AddLabelService::addLabels($order, [134], $arr, [], Auth::user()?->id);

    }
}
