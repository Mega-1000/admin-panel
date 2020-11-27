<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\OrderPaymentLog;
use App\Helpers\PriceHelper;
use App\Repositories\OrderPaymentLogRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderPaymentLogService
{
    protected $repository;

    public function __construct(OrderPaymentLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(
        int $orderId,
        int $orderPaymentId,
        int $customerId,
        string $clientPaymentAmount,
        string $orderPaymentAmount,
        string $createdAt,
        string $notices,
        string $amount,
        string $type,
        bool $sign
    ) : void {
        if($sign === true) {
            $paymentSumAfterPayment = $clientPaymentAmount + $orderPaymentAmount;
        } else {
            $paymentSumAfterPayment = $clientPaymentAmount - $orderPaymentAmount;
        }
        $this->repository->create([
            'booked_date' => $createdAt,
            'payment_type' => $type,
            'order_payment_id' => $orderPaymentId,
            'user_id' => $customerId,
            'employee_id' => Auth::user()->id,
            'order_id' => $orderId,
            'description' => $notices,
            'payment_amount' => PriceHelper::modifyPriceToValidFormat($amount),
            'payment_sum_before_payment' => $clientPaymentAmount,
            'payment_sum_after_payment' => $paymentSumAfterPayment,
        ]);
    }
}
