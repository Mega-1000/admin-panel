<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Mail\InvoiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
        $orders = Order::all();
        foreach($orders as $order) {
            $invoiceRequest = $order->invoiceRequests->first();
            if(!empty($invoiceRequest) && $invoiceRequest->status === 'MISSING') {
                try {
                    \Mailer::create()
                        ->to($order->warehouse->warehouse_email)
                        ->send(new InvoiceRequest($order->id));
                } catch (\Swift_TransportException $e) {

                }
            }
        }
    }
}
