<?php

namespace App\Services;

use App\Entities\Order;
use App\Helpers\OrderDepositPaidCalculator;
use App\Repositories\Orders;
use App\Services\Label\AddLabelService;
use Exception;
use Illuminate\Support\Facades\Auth;

readonly class OrderPaymentLabelsService
{
    public function __construct(
        protected Orders                     $orderRepository,
        protected LabelService               $labelService,
        protected OrderDepositPaidCalculator $orderDepositPaidCalculator,
    ) {}

    /**
     * @param Order $order
     * @param bool|null $calculateRelated
     * @return void
     * @oaram bool|null $calculateRelated
     *
     * @throws Exception
     */
    public function calculateLabels(Order $order, ?bool $calculateRelated = null): void
    {
        $relatedPaymentsValue = round($this->orderRepository->getAllRelatedOrderPaymentsValue($order), 2);
        $relatedOrdersValue = round($this->orderRepository->getAllRelatedOrdersValue($order), 2);
        $orderReturnGoods = round($this->orderRepository->getOrderReturnGoods($order), 2);

        $arr = [];

        if ($calculateRelated) {
            foreach ($this->orderRepository->getAllRelatedOrders($order) as $relatedOrder) {
                $this->calculateLabels($relatedOrder, false);
            }
        }

        $relatedPaymentsValue -= $orderReturnGoods;

        if (count($this->orderRepository->getAllRelatedOrderPayments($order)) === 0) {
            $this->labelService->removeLabel($order->id, [134]);
            return;
        }

        if (dd(round($relatedOrdersValue, 2), round($relatedPaymentsValue, 2))) {
            $this->labelService->removeLabel($order->id, [134]);
            AddLabelService::addLabels($order, [133], $arr, [], Auth::user()?->id);

            return;
        }

        $this->labelService->removeLabel($order->id, [133]);
        AddLabelService::addLabels($order, [134], $arr, [], Auth::user()?->id);


        $labels = $order->labels()->get()->pluck('id')->toArray();

        $labelsToCheck = [52, 53, 54, 114, 47, 48, 96, 149, 49, 50, 195, 121];
    }
}
