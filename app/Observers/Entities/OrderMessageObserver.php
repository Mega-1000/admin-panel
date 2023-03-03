<?php

namespace App\Observers\Entities;

use App\Entities\OrderMessage;
use App\Jobs\DispatchLabelEventByNameJob;

class OrderMessageObserver
{
    public function creating(OrderMessage $orderMessage)
    {
        //closing subject is done via Jobs/Orders/CloseChattingsJob as cron
        switch ($orderMessage->type) {
            case 'GENERAL':
                $this->dispatchLabels(
                    $orderMessage,
                    "chatting-started",
                    "chatting-client-sent-message-to-us",
                    "chatting-sent-message-to-client"
                );
                break;
            case 'SHIPPING':
                if ($orderMessage->source == "MAIL") {
                    dispatch(new DispatchLabelEventByNameJob($orderMessage->order,
                        "new-mail-from-shipping-company"));
                } else {
                    $this->dispatchLabels(
                        $orderMessage,
                        "chatting-started-transport",
                        "chatting-client-sent-message-to-us-transport",
                        "chatting-sent-message-to-client-transport"
                    );
                }
                break;
            case 'WAREHOUSE':
                $this->dispatchLabels(
                    $orderMessage,
                    "chatting-started-production",
                    "chatting-client-sent-message-to-us-production",
                    "chatting-sent-message-to-client-production"
                );
                break;
            case 'COMPLAINT':
                $this->dispatchLabels(
                    $orderMessage,
                    "complaint-started",
                    "complaint-client-responded",
                    "complaint-waiting-for-client"
                );
                break;
        }
    }

    /**
     * @param ?OrderMessage $orderMessage
     * @param $started
     * @param $sentToUs
     * @param $sentToClient
     * @return void
     */
    protected function dispatchLabels($orderMessage, $started, $sentToUs, $sentToClient)
    {
        if (empty($orderMessage->user_id)) {
            if ($this->isFirstMessageWithThisType($orderMessage)) {
                dispatch(new DispatchLabelEventByNameJob($orderMessage->order, $started));
            } else {
                dispatch(new DispatchLabelEventByNameJob($orderMessage->order, $sentToUs));
            }
        } else {
            dispatch(new DispatchLabelEventByNameJob($orderMessage->order, $sentToClient));
        }
    }

    protected function isFirstMessageWithThisType($orderMessage)
    {
        return $orderMessage->order->messages()->where("type", $orderMessage->type)->count() == 0;
    }
}
