<?php

namespace App\Jobs;

use App\Entities\OrderPayment;
use App\Repositories\OrderPaymentRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class CheckPromisePaymentsDates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

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
    public function handle()
    {
        $now = new Carbon('now');

        $notConfirmedPayments = OrderPayment::with('order')->where('promise', '=', 1)->get();

        if (!empty($notConfirmedPayments)) {
            foreach ($notConfirmedPayments as $notConfirmedPayment) {
                if ($this->shouldAttachLabel($notConfirmedPayment, $now)) {
                    dispatch(new AddLabelJob($notConfirmedPayment->order->id, [119]));
                } else {
                    dispatch(new RemoveLabelJob($notConfirmedPayment->order->id, [119]));
                }
            }
        }

    }

    protected function shouldAttachLabel($notConfirmedPayment, $now)
    {
        if ($notConfirmedPayment->created_at == null) {
            return;
        }
        /** @TODO toPay() value should be precalculated on events instead of calculated every single time on fly */
        if ($notConfirmedPayment->order->toPay() == 0 || $notConfirmedPayment->order->hasLabel(40)) {
            return false;
        } else {
            return $notConfirmedPayment->created_at->diff($now)->h >= 15; //only schedules that wait longer then 2h
        }

    }


}
