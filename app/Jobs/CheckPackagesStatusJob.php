<?php

namespace App\Jobs;

use App\Entities\Label;
use App\Entities\Order;
use App\Enums\PackageStatus;
use App\Factory\CourierFactory;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class CheckPackagesStatusJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ?int $userId;

    public function __construct()
    {
        $this->userId = Auth::user()?->id;
    }

    public function handle(): void
    {
        if (Auth::user() === null && $this->userId !== null) {
            Auth::loginUsingId($this->userId);
        }

        Log::info('Testing check packages status job');

        $ordersQuery = Order::whereHas('packages', function (Builder $query) {
            $query
                ->whereNotIn('status', [PackageStatus::blockedStatusVerification()])
                ->whereRaw("COALESCE(service_courier_name, '') != '' ")
                ->whereRaw("COALESCE(letter_number, '') != ''")
                ->whereDate('shipment_date', '>', Carbon::today()->addDays(-30));
        });

        $orders = $ordersQuery->get();

        Log::info('Query for packages: ' . $ordersQuery->toSql());
        Log::info('Orders with packages to update: ' . $orders->count());

        foreach ($orders as $order) {
            try {
                Log::info('Order: ' . $order->id . ' ma paczek: ' . $order->packages->count());
                foreach ($order->packages as $package) {
                    $courier = CourierFactory::create($package->service_courier_name);
                    $courier->checkStatus($package);
                    Log::info('Order package letter number: ' . $package->letter_number);
                }

                $preventionArray = [];
                if (
                    !$order
                        ->packages()
                        ->whereIn('status', [PackageStatus::SENDING, PackageStatus::WAITING_FOR_SENDING])
                        ->count()
                ) {
                    RemoveLabelService::removeLabels($order, [Label::BLUE_BATTERY_LABEL_ID], $preventionArray, [], Auth::user()?->id);
                }

                $order
                    ->packages()
                    ->where('status', PackageStatus::SENDING)
                    ->whereDate('shipment_date', '<', Carbon::today()->subDays(2)->toDateString())
                    ->get()
                    ->each(function ($package) use ($preventionArray) {
                        AddLabelService::addLabels(
                            $package->order,
                            [Label::BLUE_BATTERY_LABEL_ID],
                            $preventionArray,
                            [],
                            Auth::user()?->id
                        );
                    });
            } catch (Throwable $ex) {
                Log::error($ex->getMessage());
                continue;
            }
        }
    }
}
