<?php

namespace App\Jobs;

use App\Entities\LowOrderQuantityAlert;
use App\Entities\Order;
use App\Facades\Mailer;
use App\Mail\AlertForLowOrderQuantityMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AlertForOrderLowQuantityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public readonly Order                 $order,
        public readonly LowOrderQuantityAlert $alert,
    ) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Mailer::create()
            ->to($this->order->customer->login)
            ->send(new AlertForLowOrderQuantityMail(
                alert: $this->alert,
                order: $this->order,
            ));
    }
}
