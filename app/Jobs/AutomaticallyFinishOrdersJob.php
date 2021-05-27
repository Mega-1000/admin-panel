<?php

namespace App\Jobs;

use App\Entities\Label;
use App\Entities\Order;
use App\Entities\OrderPackage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class AutomaticallyFinishOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orders = Order
            ::whereHas('packages', function (Builder $query) {
                $query->whereIn('status', [OrderPackage::DELIVERED, OrderPackage::SENDING]);
            })
            ->whereDoesntHave('packages', function (Builder $query) {
                $query->whereNotIn('status', [OrderPackage::DELIVERED, OrderPackage::SENDING, OrderPackage::CANCELLED]);
            })
            ->whereHas('labels', function (Builder $query) {
                $query->where('labels.id', Label::ORDER_ITEMS_CONSTRUCTED);
            })
            ->whereHas('labels', function (Builder $query) {
                $query->where('labels.id', Label::BLUE_BATTERY_LABEL_ID);
            })
            ->whereDoesntHave('labels', function (Builder $query) {
                $query->where('labels.id', Label::ORDER_ITEMS_REDEEMED_LABEL);
            })
            ->whereDoesntHave('labels', function (Builder $query) {
                $query->whereIn('labels.id', [Label::ORANGE_BAG, Label::ORANGE_BATTERY_LABEL_ID]);
            })
            ->get();
        $orders->map(function ($order) {
            dispatch_now(new RemoveLabelJob($order, [Label::BLUE_BATTERY_LABEL_ID], $preventionArray, Label::ORDER_ITEMS_REDEEMED_LABEL));
        });
    }
}
