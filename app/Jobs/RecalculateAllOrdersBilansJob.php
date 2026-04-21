<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Repositories\Orders;
use App\Services\OrderPaymentLabelsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecalculateAllOrdersBilansJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @param OrderPaymentLabelsService $orderPaymentLabelsService
     * @return void
     */
    public function handle(
        OrderPaymentLabelsService $orderPaymentLabelsService,
    ): void
    {
        Orders::getAllOrdersAttachedToLabel(134)->each(function (Order $order) use ($orderPaymentLabelsService) {
            $orderPaymentLabelsService->calculateLabels($order);
        });
    }
}
