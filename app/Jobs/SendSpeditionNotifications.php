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

    public function __construct() {}

    public function updateOrderLabels($order, $newLabels): void
    {
        $allLabels = [244, 245, 74, 243, 256, 270];
        $order->labels()->detach($allLabels);

        $arr = [];
        AddLabelService::addLabels($order, [$newLabels], $arr, []);
    }

    private function nextBusinessDay(Carbon $date): Carbon
    {
        do {
            $date->addDay();
        } while (!$date->isWeekday());
        return $date;
    }

    private function previousBusinessDay(Carbon $date): Carbon
    {
        do {
            $date->subDay();
        } while (!$date->isWeekday());
        return $date;
    }

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

            if ($fromDate->isFuture() && $fromDate->isWeekday()) {
                $this->updateOrderLabels($order, [245]);
                continue;
            }

            $beforeFromDate = $this->previousBusinessDay(Carbon::create($fromDate));
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

            $askWarehouse = $order->payments()->where('declared_sum')->get();
            $haveToAskWarehouse = false;

            foreach ($askWarehouse as $item) {
                if ($item?->status !== 'Rozliczona deklarowana') {
                    $haveToAskWarehouse = true;
                    break;
                }
            }

            if (
                ($currentHour == 7 && $currentMinute < 10) ||
                ($currentHour >= 11 &&
                $haveToAskWarehouse &&
                Carbon::now()->isWeekday())
            ) {
                if (
                    $fromDate->isPast() &&
                    ($toDate->isFuture()) &&
                    !Carbon::create($order->last_confirmation)->isToday() &&
                    !$order->special_data_filled &&
                    $order?->warehouse?->shipment_after_pay_email
                ) {
                    $this->updateOrderLabels($order, [244]);

                    if ($currentHour >= 13) {
                        $this->updateOrderLabels($order, [270]);
                    }

                    if ($currentHour >= 15) {
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

            $beforeToDate = $this->previousBusinessDay(Carbon::create($toDate));
            if ($beforeToDate->isToday() && !$order->near_end_of_spedition_period_sent) {
                if ($sendMails) {
                    Mailer::create()
                        ->to($order->warehouse->shipment_after_pay_email)
                        ->send(new ReminderAboutNearEndOfSpeditionPeriod($order));
                }

                $order->update(['near_end_of_spedition_period_sent' => true]);
            }

            if ($toDate->isToday() && $toDate->isWeekday()) {
                $this->updateOrderLabels($order, [256]);
            }

            if ($sendMails && $toDate->isPast() && !$order->labels->contains('id', 66)) {
                Mailer::create()
                    ->to($order->warehouse->shipment_after_pay_email)
                    ->send(new ReminderAfterSpeditionPeriodEnded($order));
            }

            if ($toDate->isPast() && Carbon::now()->isWeekday()) {
                $this->updateOrderLabels($order, [243]);
            }
        }
    }
}
