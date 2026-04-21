<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Services\OrderPaymentLabelsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class calculateLabelsForOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public Order $order,
    ) {}

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
        $orderPaymentLabelsService->calculateLabels($this->order);
    }
}
