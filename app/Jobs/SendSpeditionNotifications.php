<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Facades\Mailer;
use App\Mail\ReminderAboutNearEndOfSpeditionPeriod;
use App\Mail\ReminderAboutStartOfSpeditionPeriod;
use App\Mail\ReminderAfterSpeditionPeriodEnded;
use App\Services\Label\AddLabelService;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSpeditionNotifications implements ShouldQueue
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
     * @throws Exception
     */
    public function handle(): void
    {
        $orders = Order::whereHas('labels', function ($query) {$query->where('labels.id', '=', 53);})->get();
        $arr = [];

        foreach ($orders as $order) {
            $fromDate = Carbon::create($order->dates->warehouse_shipment_date_from ?? $order->dates->customer_shipment_date_from);
            $toDate = Carbon::create($order->dates->warehouse_shipment_date_to ?? $order->dates->customer_shipment_date_to);

            if ($fromDate->subDay()->isToday() && !$order->start_of_spedition_period_sent) {
                Mailer::create()
                    ->to($order->warehouse->email)
                    ->send(new ReminderAboutStartOfSpeditionPeriod());

                $order->update(['start_of_spedition_period_sent' => true]);
            }

            if ($fromDate->isToday()) {
                AddLabelService::addLabels($order, [244], $arr, []);
            }

            if ($toDate->subDay()->isToday() && !$order->near_end_of_spedition_period_sent) {
                Mailer::create()
                    ->to($order->warehouse->email)
                    ->send(new ReminderAboutNearEndOfSpeditionPeriod());

                $order->update(['near_end_of_spedition_period_sent' => true]);
            }

            if ($toDate < now() && !$order->labels->includes(243)) {
                Mailer::create()
                    ->to($order->warehouse->email)
                    ->send(new ReminderAfterSpeditionPeriodEnded());

                AddLabelService::addLabels($order, [243], $arr, []);
                $order->labels()->detach(244);
            }
        }

    }
}
