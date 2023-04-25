<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Facades\Mailer;
use App\Mail\SendReminderAboutOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendReminderAboutOfferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var Order
     */
    public Order $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->order->reminder_date = null;
        $this->order->save();

        Mailer::create()
            ->to($this->order->customer->login)
            ->send(new SendReminderAboutOffer($this->order->customer));
    }
}
