<?php

namespace App\Services;

use App\Entities\Order;
use App\Helpers\OrderBilansCalculator;
use App\Helpers\OrderDepositPaidCalculator;
use App\Repositories\Orders;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
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

//        dd(round($sumOfGrossValues) + round($depositPaidData['returnedValue']) - round($depositPaidData['offerFinanceBalance']) - round($depositPaidData['wtonValue']));
        if (
            round($sumOfGrossValues) + round($depositPaidData['returnedValue']) - round($depositPaidData['offerFinanceBalance']) - round($depositPaidData['wtonValue']) == 0.0 &&
            $order->payments->count() > 0
        ) {
            $order->labels()->detach(39);
            dd('label detached', $order, $order->labels()->detach(39));
        } else {
            AddLabelService::addLabels($order, [39], $arr, [], Auth::user()?->id);
        }

        $relatedPaymentsValue -= $orderReturnGoods;

        if (count($this->orderRepository->getAllRelatedOrderPayments($order)) === 0) {
            $this->labelService->removeLabel($order->id, [134]);
            return;
        }

        if (round($relatedOrdersValue, 2) === round($relatedPaymentsValue, 2)) {
            $this->labelService->removeLabel($order->id, [134]);
            AddLabelService::addLabels($order, [133], $arr, [], Auth::user()?->id);

            return;
        }
        $this->labelService->removeLabel($order->id, [133]);
        AddLabelService::addLabels($order, [134], $arr, [], Auth::user()?->id);


        $labels = $order->labels()->get()->pluck('id')->toArray();

        $labelsToCheck = [52, 53, 54, 114, 47, 48, 96, 149, 49, 50, 195, 121];

        $labelsToCheck = array_diff($labelsToCheck, $labels);

        if (count($labelsToCheck) === 12) {
            AddLabelService::addLabels($order, [
                45, 68
            ], $arr, [], Auth::user()?->id);
        }
    }
}
