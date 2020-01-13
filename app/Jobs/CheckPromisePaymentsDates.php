<?php

namespace App\Jobs;

use App\Repositories\OrderPaymentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;

class CheckPromisePaymentsDates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrderPaymentRepository $orderPaymentRepository)
    {
        $now = new Carbon('now');

        $notConfirmedPayments = $orderPaymentRepository->findWhere(['promise' => 1]);

        if (!empty($notConfirmedPayments)) {
            foreach ($notConfirmedPayments as $notConfirmedPayment) {
                if ($this->shouldAttachLabel($notConfirmedPayment, $now)) {
                    dispatch_now(new AddLabelJob($notConfirmedPayment->order->id, [119]));
                } else {
                    dispatch_now(new RemoveLabelJob($notConfirmedPayment->order->id, [119]));
                }
            }
        }

    }

    protected function shouldAttachLabel($notConfirmedPayment, $now)
    {
        if ($notConfirmedPayment->created_at == null) {
            error_log(print_r($notConfirmedPayment, true));
            return;
        }
        if($notConfirmedPayment->order->toPay() == 0 || $notConfirmedPayment->order->hasLabel(40)) {
            return false;
        } else {
            return $notConfirmedPayment->created_at->diff($now)->h >= 15; //only schedules that wait longer then 2h
        }

    }


}
