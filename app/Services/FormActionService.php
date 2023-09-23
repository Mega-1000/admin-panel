<?php

namespace App\Services;

use App\Entities\Order;
use Illuminate\Http\JsonResponse;

class FormActionService
{
    public static function okej(Order $order): JsonResponse
    {
        dd('okej');
    }

    public static function cancel(Order $order):  JsonResponse
    {
        dd($order);
    }
}
