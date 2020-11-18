<?php

namespace App\Services;

use App\Entities\Order;
use App\Entities\OrderPayment;
use App\Entities\OrderPaymentLog;
use App\Entities\Payment;
use App\Helpers\PriceHelper;
use App\Http\Controllers\OrdersPaymentsController;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Jobs\RemoveLabelJob;
use App\Mail\WarehousePaymentAccept;
use App\Repositories\OrderPaymentLogRepository;
use App\Repositories\OrderPaymentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class OrderPaymentLogService.
 *
 * @package namespace App\Services;
 */
class OrderPaymentService
{
    protected $repository;

    public function __construct(OrderPaymentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function payOrder(int $orderId, float $amount, int $masterPaymentId, string $promise, string $chooseOrder, string $promiseDate, string $type = null, bool $isWarehousePayment = null): OrderPayment
    {
        $order = Order::find($orderId);

        if ($order->payments->count() == 0) {
            dispatch_now(new DispatchLabelEventByNameJob($orderId, "payment-received"));
            dispatch_now(new RemoveLabelJob($orderId, [44]));
        }

        if($type == null) {
            $type = 'WAREHOUSE';
        }

        if($isWarehousePayment == null) {
            $type = 'CLIENT';
        }

        $token = null;

        if($isWarehousePayment) {
            $token = md5(uniqid());
            $url = route('ordersPayment.warehousePaymentConfirmation', ['token' => $token]);
            if($order->buyInvoices()->first() !== null) {
                try {
                    \Mailer::create()
                        ->to($order->warehouse->warehouse_email)
                        ->send(new WarehousePaymentAccept($orderId, $amount, $order->buyInvoices()->first()->invoice_name, $url));
                } catch (\Swift_TransportException $e) {
                    Log::error('Warehouse payment accept email was not sent due to. Error: ' . $e->getMessage());
                }
            }
        }

        $payment = $this->repository->create([
            'amount' => str_replace(",", ".", $amount),
            'master_payment_id' => $masterPaymentId ? $masterPaymentId : null,
            'order_id' => $orderId,
            'promise' => $promise,
            'promise_date' => $promiseDate ?: null,
            'type' => $type,
            'status' => $isWarehousePayment ? 'PENDING' : null,
            'token' => $token ? $token : null
        ]);

        if ($promise == '') {
            dispatch_now(new RemoveLabelJob($orderId, [119]));
        }

        OrdersPaymentsController::dispatchLabelsForPaymentAmount($payment);

        if (!empty($chooseOrder)) {
            $masterPayment = Payment::find($masterPaymentId);
            $masterPayment->amount_left = $masterPayment->amount_left - str_replace(",", ".", $amount);
            $masterPayment->save();
            $deleted = OrderPayment::where('order_id', $orderId)
                ->where('amount', $amount)
                ->where('promise', '1')->first();

            if (!empty($deleted)) {
                $deleted->delete();
            }
        }

        if ($payment != null && $order->status_id != 5) {
            $order->status_id = 5;
            $order->save();
        }

        return $payment;
    }
}

