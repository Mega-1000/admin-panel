<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Helpers\OrdersRecalculatorBasedOnPeriod;
use App\Services\Label\AddLabelService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrdersRecalculatorBasedOnPeriodJob implements ShouldQueue
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
    public function handle(): void
    {
//        $orders = Order::whereHas('labels', function ($query) {
//            $query->whereIn('labels.id', [39, 240]);
//        })->get();
//
//        foreach ($orders as $order) {
//            OrdersRecalculatorBasedOnPeriod::recalculateOrdersBasedOnPeriod($order);
//        }

        $orders = Order::where('approved_at', '<=', now()->subDays(5))->whereHas('labels', function ($q) {
            $q->where('labels.id', 206);
        })->get();

        foreach ($orders as $order) {
            if (!$order->labels->contains([286, 220, 5, 4, 195, 221])) {
                $arr = [];
                AddLabelService::addLabels($order, [286], $arr, []);
            }
        }
    }
}
