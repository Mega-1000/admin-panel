<?php

namespace App\Services;

use App\Entities\OrderPaymentLog;
use App\Helpers\PriceHelper;
use App\Repositories\OrderPaymentLogRepository;
use Illuminate\Http\Request;

class OrderPaymentLogService
{
    protected $repository;

    public function __construct(OrderPaymentLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(int $orderId, int $orderPaymentId, int $customerId, string $clientPaymentAmount, string $orderPaymentAmount, Request $request, string $type) : void
    {
        $this->repository->create([
            'booked_date' => $request->input('created_at'),
            'payment_type' => $type,
            'order_payment_id' => $orderPaymentId,
            'user_id' => $customerId,
            'order_id' => $orderId,
            'description' => $request->input('notices'),
            'payment_amount' => PriceHelper::modifyPriceToValidFormat($request->input('amount')),
            'payment_sum_before_payment' => $clientPaymentAmount,
            'payment_sum_after_payment' => $clientPaymentAmount - $orderPaymentAmount,
        ]);
    }
}

