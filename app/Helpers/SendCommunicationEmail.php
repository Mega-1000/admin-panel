<?php


namespace App\Helpers;


use App\Helpers\interfaces\iPostOrderAction;
use App\Jobs\StartCommunicationMailSenderJob;

class SendCommunicationEmail implements iPostOrderAction
{

    public function run($order)
    {
        dispatch_now(new StartCommunicationMailSenderJob($order->id, $order->customer->login));
    }
}
