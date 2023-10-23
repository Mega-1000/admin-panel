<?php

namespace App\Repositories;

use App\DTO\orderPayments\OrderPaymentDTO;
use App\DTO\PayInImport\BankPayInDTO;
use App\Entities\Order;
use App\Entities\OrderPayment;
use App\Entities\Payment;
use Illuminate\Database\Eloquent\Model;

class OrderPayments
{
    /**
     * @param Order $order
     * @param BankPayInDTO $payIn
     *
     * @return integer
     */
   public static function getCountOfPaymentsWithDeclaredSumFromOrder(Order $order, mixed $payIn): int
   {
       $amount = $payIn->kwota ?? $payIn['kwota'];

       return $order->payments()->where('declared_sum', $amount)->whereNull('deleted_at')->count();
   }

    /**
     * @param Order $order
     * @param BankPayInDTO $payIn
     *
     * @return void
     */
    public static function updatePaymentsStatusWithDeclaredSumFromOrder(Order $order, BankPayInDTO $payIn): void
    {
        $order->payments()->where('declared_sum', $payIn->kwota)->whereNull('deleted_at')->update(['status' => 'Rozliczona deklarowana']);
    }

    /**
     * @param Order|Model $order
     * @param OrderPaymentDTO $fromArray
     * @param $operationType
     * @param OrderPayment $payment
     * @return void
     */
    public static function createRebookedOrderPayment(Order|Model $order, OrderPaymentDTO $fromArray, $operationType, OrderPayment $payment): void
    {
        $order->payments()->create([
            'amount' => $fromArray->amount,
            'payment_date' => $fromArray->payment_date,
            'payment_type' => $fromArray->payment_type,
            'status' => $fromArray->status,
            'token' => $fromArray->token,
            'operation_type' => $operationType,
            'rebooked_order_payment_id' => $payment->id,
            'payer' => $payment->payer,
        ]);
    }
}
