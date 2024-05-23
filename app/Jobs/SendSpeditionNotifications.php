<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Facades\Mailer;
use App\Mail\ReminderAboutNearEndOfSpeditionPeriod;
use App\Mail\ReminderAboutStartOfSpeditionPeriod;
use App\Mail\ReminderAfterSpeditionPeriodEnded;
use App\Mail\SpeditionDatesMonit;
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
    public function __construct() {}

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        function updateOrderLabels($order, $newLabels): void
        {
            $allLabels = [244, 245, 74, 243]; // Define all your specific labels here
            $order->labels()->detach($allLabels);
            $arr = [];
            AddLabelService::addLabels($order, $newLabels, $arr, []);
        }

        $orders = DB::table('order_labels')->where('label_id', 5)->whereDate('created_at', '>=', Carbon::now()->subMonths(3))->get();

        foreach ($orders as $order) {
            $order = Order::find($order->order_id);
            $sendMails = $order->labels->contains(53) && $order->warehouse?->warehouse_email;

            $fromDate = Carbon::create($order->dates->warehouse_shipment_date_from ?? $order->dates->customer_shipment_date_from);
            $toDate = Carbon::create($order->dates->warehouse_shipment_date_to ?? $order->dates->customer_shipment_date_to);

            if (
                $order->labels->contains(66) ||
                $order->labels->contains(230) ||
                !$order->dates
            ) {
                continue;
            }
            if ($fromDate->isFuture()) {
                updateOrderLabels($order, [245]);
            }

            $beforeFromDate = Carbon::create($fromDate)->subDay();
            if ($beforeFromDate->isToday() && !$order->start_of_spedition_period_sent) {
                if ($sendMails) {
                    Mailer::create()
                        ->to($order->warehouse->warehouse_email)
                        ->send(new ReminderAboutStartOfSpeditionPeriod($order));
                }

                $order->update(['start_of_spedition_period_sent' => true]);
            }

            $currentHour = date('H');
            $currentMinute = date('i');

            if (($currentHour == 7 && $currentMinute >= 0 && $currentMinute <= 30) || $currentHour >= 12) {
                if ($fromDate->isPast() && $toDate->isFuture() && !Carbon::create($order->last_confirmation)->isToday() && !$order->special_data_filled && $order?->warehouse?->warehouse_email) {
                    updateOrderLabels($order, [244]);

                    try {
                        Mailer::create()
                            ->to($order->warehouse->warehouse_email)
                            ->send(new SpeditionDatesMonit($order));
                    } catch (\Exception $exception) {

                    }
                }
            }

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
                updateOrderLabels($order, [256]);
            }

            if ($sendMails && $toDate->isPast()) {
                Mailer::create()
                    ->to($order->warehouse->warehouse_email)
                    ->send(new ReminderAfterSpeditionPeriodEnded($order));
            }

            if ($toDate->isPast() && !$order->labels->contains('id', 243)) {
                updateOrderLabels($order, [243]);
            }
        }
    }
}
