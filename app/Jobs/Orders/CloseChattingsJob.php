<?php

namespace App\Jobs\Orders;

use App\Jobs\DispatchLabelEventByNameJob;
use App\Jobs\Job;
use App\Repositories\OrderRepository;
use Carbon\Carbon;

class CloseChattingsJob extends Job
{
    public function handle(OrderRepository $orderRepository)
    {
        //currently there is only manual way to "Close" chatting by removing labels

        return;

        //handling other labels is done via App\Observers\Entities\OrderMessageObserver

        $now = new Carbon();
        $days = config("order-messages.days-passed-to-close-subject");

        $orders = $orderRepository->findWhere([["updated_at", ">", $now->subDay(30)]]);

        foreach ($orders as $order) {
            $generalMessage = $this->getMessageByType($order, 'GENERAL');
            if(!empty($generalMessage) && $this->isOlderThan($generalMessage, $now, $days)) {
                dispatch_now(new DispatchLabelEventByNameJob($order->id, "chatting-finished"));
            }

            $shippingMessage = $this->getMessageByType($order, 'SHIPPING');
            if(!empty($shippingMessage) && $this->isOlderThan($shippingMessage, $now, $days)) {
                dispatch_now(new DispatchLabelEventByNameJob($order->id, "chatting-finished"));
            }

            $warehouseMessage = $this->getMessageByType($order, 'WAREHOUSE');
            if(!empty($warehouseMessage) && $this->isOlderThan($warehouseMessage, $now, $days)) {
                dispatch_now(new DispatchLabelEventByNameJob($order->id, "chatting-finished"));
            }

            $complaintMessage = $this->getMessageByType($order, 'COMPLAINT');
            if(!empty($complaintMessage) && $this->isOlderThan($complaintMessage, $now, $days)) {
                dispatch_now(new DispatchLabelEventByNameJob($order->id, "complaint-closed"));
            }
        }
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
