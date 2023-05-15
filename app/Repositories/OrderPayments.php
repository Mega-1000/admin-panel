<?php

namespace App\Repositories;

use App\Entities\OrderPayment;

class OrderPayments
{
    /**
     * @param $order
     * @param $payIn
     *
     * @return integer
     */
   public static function getCountOfPaymentsWithDeclaredSumFromOrder($order, $payIn): int
   {
       return $order->payments()->where('declared_sum', $payIn['kwota'])->whereNull('deleted_at')->count();
   }

    /**
     * @param $order
     * @param $payIn
     *
     * @return void
     */
    public static function updatePaymentsStatusWithDeclaredSumFromOrder($order, $payIn): void
    {
        $order->payments()->where('declared_sum', $payIn['kwota'])->whereNull('deleted_at')->update(['status' => 'Rozliczona deklarowana']);
    }

    /**
     * @param $externalPaymentId
     *
     * @return OrderPayment|null
     */
    public static function getByExternalPaymentId($externalPaymentId): ?OrderPayment
    {
        return OrderPayment::where('external_payment_id', $externalPaymentId)->first();
    }
}
