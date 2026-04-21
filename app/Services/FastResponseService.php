<?php

namespace App\Services;

use App\Entities\FastResponse;
use App\Entities\Order;
use App\Facades\Mailer;
use App\Mail\SendFastResponseMail;

class FastResponseService
{
    public function send(FastResponse $fastResponse, Order $order): void
    {
        Mailer::create()
            ->to($order->customer->login)
            ->send(new SendFastResponseMail($fastResponse));
    }
}
