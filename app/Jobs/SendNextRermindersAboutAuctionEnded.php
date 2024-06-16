<?php

namespace App\Jobs;

use App\Entities\Label;
use App\Entities\Order;
use App\Facades\Mailer;
use App\Mail\NextRemindersAboutAuctionEndedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
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
            $query->whereIn('labels.id', 269);
        })->whereDoesntHave('labels', function ($query) {
            $query->whereIn('labels.id', 225);
        })->get();

        foreach ($orders as $order) {
            Mailer::create()
                ->to($order->customer->login)
                ->send(new NextRemindersAboutAuctionEndedMail(
                    $order
                ));
        }
    }
}
