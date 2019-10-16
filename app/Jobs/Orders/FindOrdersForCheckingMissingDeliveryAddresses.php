<?php

namespace App\Jobs\Orders;

use App\Repositories\OrderRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FindOrdersForCheckingMissingDeliveryAddresses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrderRepository $orderRepository)
    {
        $statusesToCheck = [
            5,      //w trakcie realizacji
        ];
        $orders = $orderRepository->findWhereIn("status_id", $statusesToCheck);

        if (!empty($orders)) {
            foreach ($orders as $order) {
                dispatch_now(new MissingDeliveryAddressSendMailJob($order, ["dispatch-labels-by-name" => ["missing-delivery-address"]]));
            }
        }
    }
}
