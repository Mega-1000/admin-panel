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

        $order->task->user()->detach();
        $order->task->user()->attach(37);
        AddLabelService::addLabels($order, [47], $arr, [], []);
        $order->labels()->detach(152);
        $order->chat->messages()->create([
            'user_id' => $order->chat,
            'message' => 'Tniemy na 50cm i wysyłamy',
            'type' => 'WAREHOUSE',
        ]);
    }
}
