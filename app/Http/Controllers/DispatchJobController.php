<?php

namespace App\Http\Controllers;

use App\Jobs\OrderStatusChangedNotificationJob;
use App\Repositories\OrderRepository;

class DispatchJobController extends Controller
{

    /** @var OrderRepository */
    private $orderRepository;

    /**
     * DispatchJobController constructor.
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function orderStatusChange()
    {
        $order = $this->orderRepository->find(rand(1, 10));
        $oldStatusId = $order['status_id'];
        $newStatusId = $order['status_id'] === 3 ? rand(1, 2) : $order['status_id'] + 1;
        $this->orderRepository->update([
            'status_id' => $newStatusId,
        ], $order['id']);

        dispatch_now(new OrderStatusChangedNotificationJob($order['id']));

        return [
            "orderId" => $order['id'],
            "oldStatusId" => $oldStatusId,
            "newStatusId" => $newStatusId,
        ];
    }
}
