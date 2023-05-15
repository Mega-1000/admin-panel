<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\OrderPaymentLog;
use App\Helpers\PriceHelper;
use Illuminate\Support\Facades\Auth;

class OrderPaymentLogService
{
    public function create(
        int     $orderId,
        ?int    $orderPaymentId,
        int     $customerId,
        float   $clientPaymentAmount,
        float   $orderPaymentAmount,
        string  $createdAt,
        ?string $notices,
        string  $amount,
        string  $type,
        bool    $sign,
    )
    {
        return OrderPaymentLog::query()->create([
            'booked_date' => $createdAt,
            'payment_type' => $type,
            'order_payment_id' => $orderPaymentId,
            'user_id' => $customerId,
            'employee_id' => Auth::user()->id,
            'order_id' => $orderId,
            'description' => $notices,
            'payment_amount' => PriceHelper::modifyPriceToValidFormat($amount),
            'payment_sum_before_payment' => $clientPaymentAmount,
            'payment_sum_after_payment' => $sign ? $clientPaymentAmount + $orderPaymentAmount : $clientPaymentAmount - $orderPaymentAmount,
        ]);
    }
}
