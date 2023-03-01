<?php

namespace App\Jobs;

use App\Entities\OrderPayment;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class CheckPromisePaymentsDates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    protected ?int $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->userId = Auth::user()?->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (Auth::user() === null && $this->userId !== null) {
            Auth::loginUsingId($this->userId);
        }

        $now = new Carbon('now');

        $notConfirmedPayments = OrderPayment::with('order')->where('promise', '=', 1)->get();

        if (!empty($notConfirmedPayments)) {
            foreach ($notConfirmedPayments as $notConfirmedPayment) {
                if ($this->shouldAttachLabel($notConfirmedPayment, $now)) {
                    $prev = [];
                    AddLabelService::addLabels($notConfirmedPayment->order, [119], $prev, [], null);
                } else if ($notConfirmedPayment->order->hasLabel(119)) {
                    $prev = [];
                    RemoveLabelService::removeLabels($notConfirmedPayment->order, [119], $prev, [], null);
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
