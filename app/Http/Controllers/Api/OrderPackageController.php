<?php

namespace App\Http\Controllers\Api;

use App\Entities\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class OrderPackageController extends Controller
{
    public function getByOrder(Order $order): JsonResponse
    {
        return response()->json([
            'data' => $order->packages,
        ]);
    }
}
