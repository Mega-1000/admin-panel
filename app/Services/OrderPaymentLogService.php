<?php

namespace App\Services;

use App\Entities\OrderPaymentLog;
use App\Helpers\PriceHelper;
use App\Repositories\OrderPaymentLogRepository;

/**
 * Class OrderPaymentLogService.
 *
 * @package namespace App\Services;
 */
class OrderPaymentLogService
{
    protected $repository;

    /**
     * OrderPaymentController constructor.
     *
     * @param OrderPaymentLogRepository $repository
     */
    public function __construct(OrderPaymentLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($orderId, $orderPaymentId, $customerId, $clientPaymentAmount, $orderPaymentAmount, $request, $type)
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

