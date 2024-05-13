<?php

namespace App\Jobs;

use App\Entities\OrderPaymentConfirmation;
use App\Facades\Mailer;
use App\Mail\OrderPaymentConfirmationAttachedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPaymentConfirmationProds implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $confirmations = OrderPaymentConfirmation::where('confirmed', false)
            ->where('created_at', '<', now()->subMinutes(30))
            ->get();

        foreach ($confirmations as $confirmation) {
            Mailer::create()
                ->to('antoniwoj@o2.pl')
                ->send(new OrderPaymentConfirmationAttachedMail($confirmation, true));
        }
    }
}
