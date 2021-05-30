<?php

namespace App\Jobs;

use App\Entities\Label;
use App\Entities\Order;
use App\Mail\InvoiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class CheckIfInvoicesExistInOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

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
     */
    public function handle()
    {
        $orders = Order::whereDoesntHave('invoices')->whereHas('labels', function ($query) {
            $query->where('labels.id', Label::ORDER_ITEMS_REDEEMED_LABEL);
        })->get();
        foreach($orders as $order) {
            try {
                \Mailer::create()
                    ->to($order->warehouse->warehouse_email)
                    ->send(new InvoiceRequest($order->id));
            } catch (\Swift_TransportException $e) {
                Log::error('Invoice request email has not been sent due to error: ' . $e->getMessage());
            }
        }
    }
}
