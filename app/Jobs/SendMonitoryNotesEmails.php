<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Facades\Mailer;
use App\Mail\MonitoryNotesShipmentDatePassedEmail;
use App\Repositories\Orders;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMonitoryNotesEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $orders = Orders::getOrdersNotCheckedAsShippedButRealizationDateIsPassed();

        foreach ($orders as $order) {
            $this->sendMonitoryNotesEmail($order);
        }
    }

    /**
     * @param Order $order
     * @return void
     */
    public function sendMonitoryNotesEmail(Order $order): void
    {
        Mailer::create()
            ->to($order->orderWarehouseNotifications->first()->warehouse->firm->email)
            ->send(new MonitoryNotesShipmentDatePassedEmail($order));
    }
}
