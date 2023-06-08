<?php

namespace App\Services;

use App\Entities\OrderPackage;
use App\Entities\OrderPayment;
use App\Entities\Payment;
use Illuminate\Support\Str;

class FindOrCreatePaymentForPackageService
{
    /**
     * Find or create payment for order package
     *
     * @param OrderPackage $orderPackage
     * @return Payment
     */
    public function execute(OrderPackage $orderPackage): Payment
    {
        $payment = OrderPayment::where('order_package_id', $orderPackage->id)->first();

        if ($orderPackage->cash_on_delivery > 0 && empty($payment)) {
            $orderPackage->orderPayments()->create([
                'declared_sum' => $orderPackage->cash_on_delivery,
                'type' => 'cash_on_delivery',
                'status' => 'new',
                'token' => Str::random(32),
                'order_id' => $orderPackage->order_id,
                'tracking_number' => $orderPackage->tracking_number,
            ]);

            $payment = OrderPayment::where('order_package_id', $orderPackage->id)->first();
        }

        return $payment;
    }
}
