<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Facades\Mailer;
use App\Mail\NextRemindersAboutAuctionEndedMail;
use App\Mail\ReminderAboutRealizationStarted;
use App\Services\Label\AddLabelService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNextRermindersAboutAuctionEnded implements ShouldQueue
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
     */
    public function handle()
    {
        $orders = Order::whereHas('labels', function ($query) {
            $query->where('labels.id', 269);
        })->whereDoesntHave('labels', function ($query) {
            $query->where('labels.id', 225);
        })->get();

        foreach ($orders as $order) {
            Mailer::create()
                ->to($order->customer->login)
                ->send(new NextRemindersAboutAuctionEndedMail(
                    $order
                ));
        }

        $orders = Order::whereHas('labels', function ($query) {
            $query->where('labels.id', 206);
        })->whereDoesntHave('labels', function ($query) {
            $query->where('labels.id', 225);
        })
        ->whereDoesntHave('labels', function ($query) {
            $query->where('labels.id', 220);
        })
        ->whereDoesntHave('labels', function ($query) {
            $query->where('labels.id', 221);
        })
        ->whereDoesntHave('labels', function ($query) {
            $query->where('labels.id', 5);
        })
        ->whereDoesntHave('labels', function ($query) {
            $query->where('labels.id', 272);
        })
        ->where('customer_acceptation_date', '<', now()->subDays(2))
        ->get();

        foreach ($orders as $order) {
            $arr = [];
            AddLabelService::addLabels($order, [272], $arr, []);

            Mailer::create()
                ->to($order->customer->login)
                ->send(new ReminderAboutRealizationStarted(
                    $order
                ));
        }
    }
}
