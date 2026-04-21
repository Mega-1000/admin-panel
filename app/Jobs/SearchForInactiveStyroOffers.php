<?php

namespace App\Jobs;

use App\Entities\Label;
use App\Entities\Order;
use App\Entities\OrderPaymentConfirmation;
use App\Facades\Mailer;
use App\Mail\InactiveStyroOfferNotification;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SearchForInactiveStyroOffers implements ShouldQueue
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
     * @throws Exception
     */
    public function handle(): void
    {
        $orders = Order::where()
            ->where('created_at', '<', now()->subDays(1))
            ->where()
            ->whereDoesntHave('labels', function (Builder $query) {
                $query->whereIn('labels.id', [224, 265, 52, 53, 5, 266]);
            })
            ->get();

        foreach ($orders as $order) {
            $confirmation =  OrderPaymentConfirmation::where('order_id', $order->id)->get();

            if (!$confirmation) {
                $arr = [];
                AddLabelService::addLabels($order, [266], $arr, []);
                RemoveLabelService::removeLabels($order, [224], $arr, [], null);

                Mailer::create()
                    ->to($order->customer->login)
                    ->send(new InactiveStyroOfferNotification($order));
            }
        }
    }
}
