<?php

namespace App\Services;

use App\Entities\Order;
use App\Helpers\Exceptions\ChatException;
use App\Helpers\MessagesHelper;
use App\Services\Label\AddLabelService;

class FormActionService
{

    /**
     * @throws ChatException
     */
    public static function agreeForCut(Order $order): void
    {
        $arr = [];

        $task = $order->task;
        $task->user_id = 37;
        $task->save();

        AddLabelService::addLabels($order, [47], $arr, []);
        $order->labels()->detach(152);

        $messageService = new MessagesHelper();
        $messageService->addMessage('tniemy na 50cm i wysyłamy', 5, null, null, $order->chat);
    }
}
