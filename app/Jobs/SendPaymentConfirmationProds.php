<?php

namespace App\Jobs;

use App\Entities\OrderPaymentConfirmation;
use App\Facades\Mailer;
use App\Mail\OrderPaymentConfirmationAttachedMail;
use App\Services\Label\AddLabelService;
use Exception;
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
     * @throws Exception
     */
    public function handle(): void
    {
        $confirmations = OrderPaymentConfirmation::where('confirmed', false)->where('created_at', '<', now()->subMinutes(120))->whereDoesntHave('order.labels', function ($q) {
            $q->where('labels.id', 260);
        })->get();

        foreach ($confirmations as $confirmation) {
            try {
                Mailer::create()
                    ->to($confirmation->order?->warehouse?->warehouse_email)
                    ->send(new OrderPaymentConfirmationAttachedMail($confirmation, true));

                if (!$confirmation->order->labels->has(261)) {
                    $arr = [];
                    AddLabelService::addLabels($confirmation->order, [261], $arr, []);
                    $confirmation->order->labels()->detach(259);
                }
            } catch (Exception $e) {
                echo 'okej';
            }
        }
    }
}
