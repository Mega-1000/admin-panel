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
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        $orders = DB::table('order_labels')->where('label_id', 5)->whereDate('created_at', '>=', Carbon::now()->subMonths(3))->get();

        foreach ($orders as $order) {
            $order = Order::find($order->order_id);
            $sendMails = $order->labels->contains(53) && $order->warehouse?->warehouse_email;

            if ($order->labels->contains(66) || !$order->dates) {
                continue;
            }

            $fromDate = Carbon::create($order->dates->warehouse_shipment_date_from ?? $order->dates->customer_shipment_date_from);
            $toDate = Carbon::create($order->dates->warehouse_shipment_date_to ?? $order->dates->customer_shipment_date_to);

            if ($fromDate->isFuture()) {
                $arr = [];
                AddLabelService::addLabels($order, [245], $arr, []);
            }

            // Check if the day before the from date is today
            $beforeFromDate = Carbon::create($fromDate)->subDay();
            if ($beforeFromDate->isToday() && !$order->start_of_spedition_period_sent) {
                if ($sendMails) {
                    Mailer::create()
                        ->to($order->warehouse->warehouse_email)
                        ->send(new ReminderAboutStartOfSpeditionPeriod($order));
                }

                $order->update(['start_of_spedition_period_sent' => true]);
            }

            if ($fromDate->isPast() && $toDate->isFuture()) {
                $arr = [];
                AddLabelService::addLabels($order, [244], $arr, []);

                $order->labels()->detach(245);
            }

            // Check if the day before the to date is today
            $beforeToDate = Carbon::create($toDate)->subDay();
            if ($beforeToDate->isToday() && !$order->near_end_of_spedition_period_sent) {
                if ($sendMails) {
                    Mailer::create()
                        ->to($order->warehouse->warehouse_email)
                        ->send(new ReminderAboutNearEndOfSpeditionPeriod($order));
                }

                $order->update(['near_end_of_spedition_period_sent' => true]);
            }

            if ($toDate->isToday()) {
                $order->labels()->detach(244);

                $arr = [];
                AddLabelService::addLabels($order, [74], $arr, []);
            }

            if ($toDate->isPast() && !$order->labels->contains('id', 243)) {
                if ($sendMails) {
                    Mailer::create()
                        ->to($order->warehouse->warehouse_email)
                        ->send(new ReminderAfterSpeditionPeriodEnded($order));
                }

                $arr = [];
                AddLabelService::addLabels($order, [243], $arr, []);
                $order->labels()->detach(74);
            }
        }
    }
}
