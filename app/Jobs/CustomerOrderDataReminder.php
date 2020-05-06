<?php

namespace App\Jobs;

use App\Entities\Label;
use App\Entities\Order;
use App\Mail\ConfirmData;
use App\Mail\DifferentCustomerData;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class CustomerOrderDataReminder implements ShouldQueue
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
        $orders = Order::whereHas('labels', function ($query) {
            $query->whereIn('id', Label::CUSTOMER_DATA_REMINDER_IDS);
        })->get();

        foreach($orders as $order) {
            $noData = DB::table('gt_invoices')->where('order_id', $order->id)->where('gt_invoice_status_id', '13')->first();
            if (!empty($noData)) {
                $senderJob = new DifferentCustomerData('Wybór danych do wystawienia faktury - zlecenie '.$order->id, $order->id, $noData->id);
            } else {
                $senderJob = new ConfirmData('Wybór danych do wystawienia faktury  - zlecenie'.$order->id, $order->id)
            }

            try {
                \Mailer::create()
                    ->to($order->customer->login)
                    ->send($senderJob);
            } catch (\Swift_TransportException $e) {
                Log::error('Customer order data change email has not been sent due to: ', $e->getMessage());
            }
        }
    }
}
