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

    public function updateOrderLabels ($order, $newLabels): void
    {
        $allLabels = [244, 245, 74, 243, 256, 270]; // Define all your specific labels here
        $order->labels()->detach($allLabels);
        $arr = [];
        AddLabelService::addLabels($order, $newLabels, $arr, []);
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        $orders = DB::table('order_labels')->where('label_id', 53)->whereDate('created_at', '>=', Carbon::now()->subMonths(3))->get();

        foreach ($orders as $order) {
            $order = Order::find($order->order_id);
            $sendMails = $order->labels->contains('id', 53) &&
                $order->warehouse?->shipment_after_pay_email;

            if ($order->labels->contains('id', 179)) {
                continue;
            }

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
                $this->updateOrderLabels($order, [245]);
            }


            $beforeFromDate = Carbon::create($fromDate)->subDay();
            if ($beforeFromDate->isToday() && !$order->start_of_spedition_period_sent) {
                if ($sendMails) {
                    Mailer::create()
                        ->to($order->warehouse->shipment_after_pay_email)
                        ->send(new ReminderAboutStartOfSpeditionPeriod($order));
                }

                $order->update(['start_of_spedition_period_sent' => true]);
            }

            $currentHour = date('H');
            $currentMinute = date('i');

            $haveToAskWarehouse = $order->payments()->where('declared_sum')->get();

            foreach ($haveToAskWarehouse as $item) {
                if ($item?->status !== 'Rozliczona deklarowana') {
                    $haveToAskWarehouse = true;
                    break;
                }
            }

            // Jeśli datach wysyłki zamówienia zawiera się data obecna dodaję etykietę 244 i wysyłam prośbę o wypełnienie danych specjalnych do fabryki
            // o 11:00 zaczyna się wysyłanie maili do fabryki co 15 minut
            // jeśli fabryka nie wypełni danych specjalnych do godziny 14:00 to dodaję etykietę 270
            if (
                ($currentHour == 7 && $currentMinute >= 0 && $currentMinute <= 30) ||
                $currentHour >= 11 &&
                $haveToAskWarehouse

            ) {
                if (
                    $fromDate->isPast() &&
                    ($toDate->isFuture() || $toDate->isToday()) &&
                    !Carbon::create($order->last_confirmation)->isToday() &&
                    !$order->special_data_filled &&
                    $order?->warehouse?->shipment_after_pay_email
                ) {
                    $this->updateOrderLabels($order, [244]);

                    if ($currentHour >= 11) {
                        $this->updateOrderLabels($order, [270]);
                    }

                    if ($currentHour >= 14) {
                        $this->updateOrderLabels($order, [275]);
                    }
                    if ($sendMails) {
                        Mailer::create()
                            ->to($order->warehouse->shipment_after_pay_email)
                            ->send(new SpeditionDatesMonit($order));

                        $order->labels_log .= 'Wysłano email dotyczący prośby określenia daty wyjazdu ' . date('Y-m-d H:i:s') . ' przez ' . PHP_EOL;

                    }
                }
            }

            $beforeToDate = Carbon::create($toDate)->subDay();
            if ($beforeToDate->isToday() && !$order->near_end_of_spedition_period_sent) {
                if ($sendMails) {
                    Mailer::create()
                        ->to($order->warehouse->shipment_after_pay_email)
                        ->send(new ReminderAboutNearEndOfSpeditionPeriod($order));
                }

                $order->update(['near_end_of_spedition_period_sent' => true]);
            }

            if ($toDate->isToday()) {
                $this->updateOrderLabels($order, [256]);
            }

            if ($sendMails && $toDate->isPast() && !$order->end_of_spedition_period_sent) {
                Mailer::create()
                    ->to($order->warehouse->shipment_after_pay_email)
                    ->send(new ReminderAfterSpeditionPeriodEnded($order));

                $order->end_of_spedition_period_sent = true;
                $order->save();
            }

            if ($toDate->isPast() && !$order->labels->contains('id', 243)) {
                $this->updateOrderLabels($order, [243]);
            }
        }
    }
}
