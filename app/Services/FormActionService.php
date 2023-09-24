<?php

namespace App\Services;

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

        AddLabelService::addLabels($order, [47], $arr, [], []);
        $order->labels()->detach(152);
        $order->chat->messages()->create([
            'user_id' => $order->chat,
            'message' => 'Tniemy na 50cm i wysyłamy',
            'type' => 'WAREHOUSE',
        ]);
    }
}
