<?php

namespace App\Jobs\Orders;

use App\Jobs\Job;
use App\Repositories\OrderRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class CloseChattingsJob extends Job implements ShouldQueue
{
    use IsMonitored;

    /** @deprecated - to remove in next versions */
    public function handle(OrderRepository $orderRepository)
    {
        /** Deprecated to remove in next versions */
//        $now = new Carbon();
//        $days = config("order-messages.days-passed-to-close-subject");
//
//        $orders = $orderRepository->findWhere([["updated_at", ">", $now->subDays(30)]]);
//
//        foreach ($orders as $order) {
//            $generalMessage = $this->getMessageByType($order, 'GENERAL');
//            if(!empty($generalMessage) && $this->isOlderThan($generalMessage, $now, $days)) {
//                dispatch_now(new DispatchLabelEventByNameJob($order->id, "chatting-finished"));
//            }
//
//            $shippingMessage = $this->getMessageByType($order, 'SHIPPING');
//            if(!empty($shippingMessage) && $this->isOlderThan($shippingMessage, $now, $days)) {
//                dispatch_now(new DispatchLabelEventByNameJob($order->id, "chatting-finished"));
//            }
//
//            $warehouseMessage = $this->getMessageByType($order, 'WAREHOUSE');
//            if(!empty($warehouseMessage) && $this->isOlderThan($warehouseMessage, $now, $days)) {
//                dispatch_now(new DispatchLabelEventByNameJob($order->id, "chatting-finished"));
//            }
//
//            $complaintMessage = $this->getMessageByType($order, 'COMPLAINT');
//            if(!empty($complaintMessage) && $this->isOlderThan($complaintMessage, $now, $days)) {
//                dispatch_now(new DispatchLabelEventByNameJob($order->id, "complaint-closed"));
//            }
//        }
    }

    protected function isOlderThan($message, $now, $days)
    {
        return (new Carbon($message->created_at))->lessThan($now->subDay($days));
    }

    protected function getMessageByType($order, $type)
    {
        return $order->messages()->where("type", $type)->orderBy("created_at", 'desc')->first();
    }

}
