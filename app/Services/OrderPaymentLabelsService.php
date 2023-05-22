<?php

namespace App\Services;

use App\Entities\Order;
use App\Repositories\Orders;
use App\Services\Label\AddLabelService;
use Illuminate\Support\Facades\Auth;

class OrderPaymentLabelsService
{
    public function __construct(
        protected Orders $orderRepository,
        protected LabelService $labelService,
    ) {
    }
    /**
     * @param Order $order
     *
     * @return void
     */
    public function calculateLabels(Order $order): void
    {
        $relatedPaymentsValue = round($this->orderRepository->getAllRelatedOrderPaymentsValue($order), 2);
        $relatedOrdersValue = round($this->orderRepository->getAllRelatedOrdersValue($order), 2);
        $orderReturnGoods = $this->orderRepository->getOrderReturnGoods($order);

        $relatedPaymentsValue += $orderReturnGoods;
        $arr = [];

        if ($this->orderRepository->getAllRelatedOrderPayments($order)->count() === 0) {
            $this->labelService->removeLabel($order->id, [134]);
            return;
        }

        if ($relatedPaymentsValue === $relatedOrdersValue) {
            $this->labelService->removeLabel($order->id, [134]);
            AddLabelService::addLabels($order, [133], $arr, [], Auth::user()?->id);

            return;
        }
        $this->labelService->removeLabel($order->id, [133]);
        AddLabelService::addLabels($order, [134], $arr, [], Auth::user()?->id);


        $labels = $order->labels()->get()->pluck('id')->toArray();
        $labelsToCheck = [52, 53, 54, 114, 47, 48, 96, 149, 49, 50];
        $labelsToCheck = array_diff($labelsToCheck, $labels);

        if (count($labelsToCheck) === 10) {
            AddLabelService::addLabels($order, [45, 68], $arr, [], Auth::user()?->id);
        }
    }
}
