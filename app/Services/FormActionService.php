<?php

namespace App\Services;

use App\Entities\Message;
use App\Entities\Order;
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

        $messageService = new MessageService();
        $messageService->createMessage($order->chat, $order->chat->employee, 'tniemy na 50cm i wysyłamy', 'order', $arr);
    }
}
