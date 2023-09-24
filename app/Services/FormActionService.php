<?php

namespace App\Services;

use App\DTO\AllegroPayment\AllegroReturnDTO;
use App\Entities\Order;
use App\Helpers\AllegroReturnPaymentHelper;
use App\Helpers\Exceptions\ChatException;
use App\Helpers\MessagesHelper;
use App\Services\Label\AddLabelService;

class FormActionService
{
    /**
     * @throws ChatException
     */
    public static function agreeForCut(Order $order): void
    {
        $arr = [];

        $task = $order->task;
        $task->user_id = 37;
        $task->save();

        AddLabelService::addLabels($order, [47], $arr, []);
        $order->labels()->detach(152);

        $messageService = new MessagesHelper();
        $messageService->addMessage('tniemy na 50cm i wysyłamy', 5, null, null, $order->chat);
    }

    public static function agreeForAdditionalPay(Order $order): void
    {
        $arr = [];

        $task = $order->task;
        $task->user_id = 37;
        $task->save();

        $order->additional_cash_on_delivery_cost += 29.90;
        $order->save();


        AddLabelService::addLabels($order, [47], $arr, []);
        $order->labels()->detach(152);


        $package = $order->packages()->first();
        $package->cash_on_delivery += 29.90;
        $package->save();
    }

    public static function cancelOrder(Order $order): void
    {
        $arr = [];
        AddLabelService::addLabels($order, [178], $arr, []);
        $allegroOrderService = app(AllegroOrderService::class);
        $allegroPaymentService = app(AllegroPaymentService::class);

        $allegroPaymentId = $order->allegro_payment_id;
        $allegroOrder = $allegroOrderService->getOrderByPaymentId($allegroPaymentId);

        $returnsByAllegroId = AllegroReturnPaymentHelper::createReturnsByAllegroId($allegroOrder, []);

        list($lineItemsForPaymentRefund, $lineItemsForCommissionRefund) = AllegroReturnPaymentHelper::createLineItemsFromReturnsByAllegroId($returnsByAllegroId);

        $data = new AllegroReturnDTO(
            paymentId: $allegroPaymentId,
            reason: $request->reason,
            lineItems: $lineItemsForPaymentRefund,
        );

        $allegroPaymentService->initiatePaymentRefund($data);
    }
}
