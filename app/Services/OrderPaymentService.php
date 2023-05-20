<?php
declare(strict_types=1);

namespace App\Services;

use App\Entities\Label;
use App\Entities\Order;
use App\Entities\OrderPayment;
use App\Enums\LabelEventName;
use App\Enums\OrderPaymentPayer;
use App\Enums\OrderPaymentPromiseType;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Helpers\PriceHelper;
use App\Helpers\TokenHelper;
use App\Http\Controllers\OrdersPaymentsController;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use Illuminate\Database\Eloquent\Model;

class OrderPaymentService
{
    protected $orderPaymentRepository;
    protected $orderRepository;
    protected $paymentRepository;
    protected $labelService;
    protected $orderPaymentMailService;

    public function __construct(
        OrderPaymentRepository  $orderPaymentRepository,
        OrderRepository         $orderRepository,
        PaymentRepository       $paymentRepository,
        LabelService            $labelService,
        OrderPaymentMailService $orderPaymentMailService
    )
    {
        $this->orderPaymentRepository = $orderPaymentRepository;
        $this->orderRepository = $orderRepository;
        $this->paymentRepository = $paymentRepository;
        $this->labelService = $labelService;
        $this->orderPaymentMailService = $orderPaymentMailService;
    }

    // TODO TYPOWANIA POPRAWIć!!!
    public function payOrder(
        int     $orderId,
        string  $amount,
        string  $payer,
        ?string $masterPaymentId,
        string  $promise,
                $chooseOrder,
        string  $promiseDate,
        string  $type = null,
        bool    $isWarehousePayment = null
    ): OrderPayment
    {
        $order = $this->orderRepository->find($orderId);

        if ($order->payments->count() == 0) {
            $this->labelService->dispatchLabelEventByNameJob($order->id, LabelEventName::PAYMENT_RECEIVED);
            $this->labelService->removeLabel($orderId, [Label::ORDER_FOR_REALISATION]);
        }

        if ($type == null) {
            $type = OrderPaymentPayer::WAREHOUSE;
        }

        if ($isWarehousePayment == null) {
            $type = OrderPaymentPayer::CLIENT;
        }

        $token = null;

        if ($isWarehousePayment) {
            $token = TokenHelper::generateMD5Token();
            if ($order->buyInvoices()->first() !== null) {
                $this->orderPaymentMailService->sendWarehousePaymentAcceptMail(
                    $order->warehouse->warehouse_email,
                    $orderId,
                    $amount,
                    $order->buyInvoices()->first()->invoice_name,
                    $token
                );
            }
        }

        $payment = $order->payments()->create([
            'declared_sum' => PriceHelper::modifyPriceToValidFormat($amount),
            'master_payment_id' => $masterPaymentId ?: null,
            'promise' => $promise,
            'promise_date' => $promiseDate ?: null,
            'type' => $type,
            'status' => $isWarehousePayment ? OrderPaymentStatus::PENDING : null,
            'token' => $token ?: null,
            'payer' => $payer,
        ]);

        if ($promise == OrderPaymentPromiseType::BOOKED) {
            $this->labelService->removeLabel($orderId, [Label::IS_NOT_PAID]);
        }

        OrdersPaymentsController::dispatchLabelsForPaymentAmount($payment);

        if (!empty($chooseOrder)) {
            $this->removePromisedPayment($masterPaymentId, $amount, $orderId);
        }

        if ($payment != null && $order->status_id != OrderStatus::IN_REALISATION) {
            $order->status_id = OrderStatus::IN_REALISATION;
            $order->save();
        }

        return $payment;
    }

    public function hasAnyPayment(Order $order): bool
    {
        return $order->payments->count() > 0;
    }

    private function removePromisedPayment(string $masterPaymentId, string $amount, int $orderId): void
    {
        $masterPayment = $this->paymentRepository->find($masterPaymentId);
        $masterPayment->amount_left = $masterPayment->amount_left - PriceHelper::modifyPriceToValidFormat($amount);
        $masterPayment->save();
        $deleted = $this->orderPaymentRepository->getPromisedPayment($orderId, $amount);

        if (!empty($deleted)) {
            $deleted->delete();
        }
    }

    public static function createReturn(Order $order, array $request): Model
    {
        return $order->payments()->create([
            'amount' => $request['return_value'] * -1,
            'notices' => $request['notices'],
            'type' => OrderPaymentPayer::CLIENT,
            'operation_type' => 'Zwrot towaru',
            'payer' => $request['payer'],
        ]);
    }
}
