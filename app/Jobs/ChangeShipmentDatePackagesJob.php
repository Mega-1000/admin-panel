<?php

namespace App\Jobs;

use App\Helpers\OrderPackagesDataHelper;
use App\Repositories\OrderPackageRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;

class ChangeShipmentDatePackagesJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderPackageRepository;

    protected $orderPackagesDataHelper;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        OrderPackageRepository $orderPackageRepository,
        OrderPackagesDataHelper $orderPackagesDataHelper
    )
    {
        $this->orderPackageRepository = $orderPackageRepository;
        $this->orderPackagesDataHelper = $orderPackagesDataHelper;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $packages = $this->orderPackageRepository->findWhere([
            ['shipment_date', '<', Carbon::today()],
        ])->whereIn('status', ['WAITING_FOR_SENDING', 'NEW']);
        foreach ($packages as $package) {
            $date = new Carbon($package->shipment_date);
            if ($date->toDateString() < Carbon::today()->toDateString()) {
                $shipmentDate = Carbon::today();
                $array = [
                    'shipment_date' => $shipmentDate,
                    'delivery_date' => $this->orderPackagesDataHelper->calculateDeliveryDate($shipmentDate),
                ];
                if ($package->order != null) {
                    $package->order->shipment_date = $shipmentDate;
                    $package->order->update();
                    $this->orderPackageRepository->update($array, $package->id);
                    if ($package->order->task != null) {
                        $dateStart = new Carbon($package->order->task->taskTime->date_start);
                        $dateEnd = new Carbon($package->order->task->taskTime->date_end);
                        $package->order->task->taskTime->update([
                            'date_start' => $dateStart->addDay()->toDateTimeString(),
                            'date_end' => $dateEnd->addDay()->toDateTimeString(),
                        ]);
                    }
                }
            }
        }
    }
}
