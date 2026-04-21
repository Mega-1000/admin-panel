<?php

namespace App\Jobs;

use App\Entities\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckAdvandedInvoiceNeed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(){}

    public function handle(): void
    {
        $orders = Order::whereHas('labels', function ($q) {
            $q->where('labels.id', 288);
        });

        foreach ($orders as $order) {
            $order->preferred_invoice_date = now();
            $order->save();
        }
    }
}
