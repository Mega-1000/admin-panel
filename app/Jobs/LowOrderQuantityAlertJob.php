<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Services\LowOrderQuantityAlertService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LowOrderQuantityAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private readonly Order $order
    ){}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        (new LowOrderQuantityAlertService())->dispatchAlertsForOrder($this->order);
    }
}
