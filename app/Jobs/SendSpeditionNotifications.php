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
use Illuminate\Support\Facades\DB;

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
        $orders = DB::table('order_labels')->where(['label_id' => 53])->get();

        foreach ($orders as $order) {
            $order = Order::find($order->order_id);

            if ($order->id == 85460) {

            dd($order->warehouse?);
            }
            if ($order->labels->contains(66)) {
                continue;
            }

            if (!$order->dates || !$order->warehouse?->customer_shipment_date_from) {
                continue;
            }

            $fromDate = Carbon::create($order->dates->warehouse_shipment_date_from ?? $order->dates->customer_shipment_date_from);
            $toDate = Carbon::create($order->dates->warehouse_shipment_date_to ?? $order->dates->customer_shipment_date_to);
            dd($fromDate, $toDate);
            if ($fromDate->isFuture()) {
                $arr = [];
                AddLabelService::addLabels($order, [245], $arr, []);
            }

            if ($fromDate->subDay()->isToday() && !$order->start_of_spedition_period_sent) {
                Mailer::create()
                    ->to($order->warehouse->warehouse_email)
                    ->send(new ReminderAboutStartOfSpeditionPeriod($order));

                $order->update(['start_of_spedition_period_sent' => true]);
            }

            if ($fromDate->isToday()) {
                $arr = [];
                AddLabelService::addLabels($order, [244], $arr, []);

                $order->labels()->detach(245);
            }

            if ($toDate->subDay()->isToday() && !$order->near_end_of_spedition_period_sent) {
                Mailer::create()
                    ->to($order->warehouse->warehouse_email)
                    ->send(new ReminderAboutNearEndOfSpeditionPeriod($order));

                $order->update(['near_end_of_spedition_period_sent' => true]);
            }

            if ($toDate->isToday()) {
                $order->labels()->detach(244);

                $arr = [];
                AddLabelService::addLabels($order, [74], $arr, []);
            }

            if ($toDate->isPast() && !$order->labels->contains('id', 243)) {
                Mailer::create()
                    ->to($order->warehouse->warehouse_email)
                    ->send(new ReminderAfterSpeditionPeriodEnded($order));

                $arr = [];
                AddLabelService::addLabels($order, [243], $arr, []);
                $order->labels()->detach(74);
            }
        }

    }
}
