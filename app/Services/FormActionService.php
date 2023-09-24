<?php

namespace App\Services;

use App\Entities\Message;
use App\Entities\Order;
use App\Helpers\MessagesHelper;
use App\Services\Label\AddLabelService;
use Illuminate\Http\JsonResponse;

class FormActionService
{

    public static function agreeForCut(Order $order): void
    {
        $arr = [];

        $task = $order->task;
        $task->user_id = 37;
        $task->save();

        AddLabelService::addLabels($order, [47], $arr, []);
        $order->labels()->detach(152);

        $messageService = new MessagesHelper();
        $messageService->addMessage('tniemy na 50cm i wysyłamy', 2, null, null, $order->chat);
    }
}
