<?php

namespace App\Jobs;

use App\Entities\Label;
use App\Entities\Order;
use App\Enums\PackageStatus;
use App\Facades\Mailer;
use App\Mail\RemindAboutInvoice;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class AutomaticallyFinishOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    protected ?int $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->userId = Auth::user()?->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (Auth::user() === null && $this->userId !== null) {
            Auth::loginUsingId($this->userId);
        }

        $orders = Order
            ::whereHas('packages', function (Builder $query) {
                $query->whereIn('status', [PackageStatus::DELIVERED, PackageStatus::SENDING]);
            })
            ->whereDoesntHave('packages', function (Builder $query) {
                $query->whereNotIn('status', [PackageStatus::DELIVERED, PackageStatus::SENDING, PackageStatus::CANCELLED]);
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
//            $preventionArray = [];
//            RemoveLabelService::removeLabels($order, [Label::BLUE_BATTERY_LABEL_ID], $preventionArray, [Label::ORDER_ITEMS_REDEEMED_LABEL], Auth::user()?->id);
        });


        // if now is working day
        if (now()->isWeekday()) {
            $orders = Order::where('shipped_at', '>=', now()->subDays(5))
                ->where('calculated_shipping_invoices', false)
                ->get();

            foreach ($orders as $order) {
                if ($order->labels->includes(231)) {
                    $arr = [];
                    AddLabelService::addLabels($order, [289], $arr, []);
                }

                 if ($order->labels->includes(263)) {
                     $arr = [];
                     AddLabelService::addLabels($order, [290], $arr, []);
                 }

                $order->calculated_shipping_invoices = true;
                $order->save();
            }
        }

        $orders = Order::whereHas('labels', function ($q) {
            $q->where('labels.id', 290);
        })->get();

        foreach ($orders as $order) {
            Mailer::create()
                ->to()
                ->send(new RemindAboutInvoice($order));
        }
    }
}
