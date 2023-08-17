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

        $orders = Order::whereHas('packages', function ($query) {
            $query
                ->whereIn('status', ['SENDING', 'WAITING_FOR_SENDING'])
                ->whereDate('shipment_date', '>', Carbon::today()->subDays(30)->toDateString());
        })->get();

        foreach ($orders as $order) {
            try {
                foreach ($order->packages as $package) {
                    if ($package->status == PackageStatus::DELIVERED ||
                        $package->status == PackageStatus::WAITING_FOR_CANCELLED ||
                        $package->status == PackageStatus::CANCELLED ||
                        $package->status == PackageStatus::REJECT_CANCELLED ||
                        empty($package->letter_number)) {
                        continue;
                    }

                    $courier = CourierFactory::create($package->service_courier_name);
                    $courier->checkStatus($package);
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
