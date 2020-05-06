<?php

namespace App\Jobs;

use App\Entities\Label;
use App\Entities\Order;
use App\Entities\InvoiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class UrgentInvoiceRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $orders = Order::whereHas('invoiceRequests', function ($query) {
            $query->where('invoiceRequests.status', InvoiceRequest::STATUS_MISSING);
        })->get();
        foreach($orders as $order) {
            $invoiceRequest = $order->invoiceRequests()->first();
            try {
                \Mailer::create()
                    ->to($order->warehouse->warehouse_email)
                    ->send(new InvoiceRequest($order->id));
            } catch (\Swift_TransportException $e) {
                Log::error('Urgent invoice email request to warehouse was not sent due to. Error: ' . $e->getMessage());
            }
        }
    }
}
