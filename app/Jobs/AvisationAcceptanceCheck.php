<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Services\Label\AddLabelService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AvisationAcceptanceCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * @throws Exception
     */
    public function handle(): void
    {
        $this->order = Order::find($this->order->id);

        if (!$this->order->labels->has(53)) {
            $arr = [];
            if (!$this->order->labels->has(300)) {
                AddLabelService::addLabels($this->order, [77], $arr, []);
            }

            $this->order->labels()->detach(53);
        }

        $notification = $this->order->orderWarehouseNotifications()->latest()->first();

        if (
            $notification?->delayed_to &&
            $notification?->delayed_to > now()
        ) {
            $this->order->labels()->detach(77);
            $this->order->labels()->detach(300);
            $this->order->labels()->attach(52);

            self::dispatch($this->order)->delay($notification->delayed_to);
        }
    }
}
