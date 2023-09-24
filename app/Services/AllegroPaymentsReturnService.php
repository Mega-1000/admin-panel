<?php

namespace App\Services;

use App\Entities\Label;
use App\Entities\LabelGroup;
use App\Entities\Order;
use App\Entities\OrderReturn;
use App\Enums\OrderPaymentsEnum;
use App\Repositories\Orders;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use Illuminate\Support\Facades\Auth;

readonly class AllegroPaymentsReturnService
{
    public function __construct(
        private AllegroPaymentService $allegroPaymentService,
        private Orders $orderRepository,
    ) {}

    /**
     * @param Order $order
     * @return void
     */
    public static function checkAllegroReturn(Order $order): void
    {
        /** @var $orderLabels */
        $orderLabels = $order->labels()->pluck('labels.id')->toArray();

        if (in_array(50, $orderLabels) &&) {
            dd('okej')
        }

        if (
            in_array(Label::RETURN_ALLEGRO_PAYMENTS, $orderLabels) &&
            !in_array(Label::ORDER_ITEMS_REDEEMED_LABEL, $orderLabels) &&
            !self::checkIfOrderHasKwonPayment($order)
        ) {
            $order->payments()->create([
                'amount' => $order->getValue(),
                'operation_type' => OrderPaymentsEnum::KWON_STATUS,
                'payer' => $order->customer->login,
            ]);
        }
    }

    /**
     * @param Order $order
     * @return bool
     */
    private static function checkIfOrderHasKwonPayment(Order $order): bool
    {
        return $order->payments()->where('operation_type', OrderPaymentsEnum::KWON_STATUS)->exists();
    }

    public function getOrderItemsWithReturns(Order $order): ?Order
    {
        if (count($order->items) === 0) {
            return null;
        }

        $orderIsConstructed = $this->orderRepository->orderIsConstructed($order);

        $hasOrderReturn = false;

        foreach ($order->items as $item) {
            $productId = $item->product_id;
            $orderReturn = OrderReturn::with(['product'])->where('product_id', $productId)->where('order_id', $order->id)->orderByDesc('created_at')->first();
            if (empty($orderReturn)) {
                if ($orderIsConstructed) {
                    $item['orderReturn'] = null;
                    continue;
                }

                $dummyReturn = new OrderReturn();
                $dummyReturn->quantity_undamaged = $item->quantity;
                $dummyReturn->quantity_damaged = 0;
                $item['orderReturn'] = $dummyReturn;
                continue;
            }
            $item['orderReturn'] = $orderReturn;
            $hasOrderReturn = true;
        }

       return ($hasOrderReturn || !$orderIsConstructed) ? $order : null;
    }

    public function returnCommissionsAndReturnFailed(array $lineItemsForCommissionRefund, array $returnsByAllegroId) {
        $unsuccessfulCommissionRefundsItemNames = [];

        foreach ($lineItemsForCommissionRefund as $lineItem) {
            $commissionRefundCreatedSuccessfully = $this->allegroPaymentService->createCommissionRefund($lineItem['id'], $lineItem['quantity']);
            if (!$commissionRefundCreatedSuccessfully) {
                $itemName = $returnsByAllegroId[$lineItem['id']]['name'];
                $unsuccessfulCommissionRefundsItemNames[] = $itemName;
            }
        }

        return $unsuccessfulCommissionRefundsItemNames;
    }

    public function removeAndAddNeccessaryLabelsAfterAllegroReturn(Order $order): void
    {
        $loopPreventionArray = [];
        RemoveLabelService::removeLabels($order, [Label::NEED_TO_RETURN_PAYMENT], $loopPreventionArray, [], Auth::user()?->id);
        $loopPreventionArray = [];
        AddLabelService::addLabels($order, [Label::NEED_TO_ISSUE_INVOICE_CORRECTION], $loopPreventionArray, [], Auth::user()?->id);

        if (!$this->orderRepository->orderIsConstructed($order)) {
            $transportLabels = LabelGroup::query()->find(LabelGroup::TRANSPORT_LABEL_GROUP_ID)->labels()->pluck('labels.id')->toArray();
            $toRemove = [Label::BLUE_HAMMER_ID, Label::RED_HAMMER_ID, Label::ORDER_ITEMS_UNDER_CONSTRUCTION];
            $toRemove = array_merge($toRemove, $transportLabels);
            $order->labels()->detach($toRemove);
        }
    }
}
