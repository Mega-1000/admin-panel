<?php

namespace App\Jobs\Orders;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Entities\Order;
use App\Entities\OrderOffer;
use App\Entities\Status;

class ChangeOrderStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->order->status->generate_order_offer === 1) {
            $orderOfferMessage = Status::find(18)->message;

            $orderOffer = OrderOffer::firstOrNew(['order_id' => $this->order->id, 'message' => $orderOfferMessage]);
            $orderOffer->message = $orderOfferMessage;
            $orderOffer->save();
        }
    }
}
