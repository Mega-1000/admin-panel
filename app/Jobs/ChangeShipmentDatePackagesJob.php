<?php

namespace App\Jobs;

use App\Helpers\OrderPackagesDataHelper;
use App\Repositories\OrderPackageRepository;
use App\Repositories\OrderRepository;
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

    protected $orderRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        OrderPackageRepository $orderPackageRepository,
        OrderPackagesDataHelper $orderPackagesDataHelper,
        OrderRepository $orderRepository
    )
    {
        $this->orderPackageRepository = $orderPackageRepository;
        $this->orderPackagesDataHelper = $orderPackagesDataHelper;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orders = $this->orderRepository->findWhere([
            ['shipment_date', '<', Carbon::today()],
        ]);
        foreach($orders as $order) {
            $date = new Carbon($order->shipment_date);
            if($order->hasLabel(66)) {
                continue;
            }
            if ($order->packages->isEmpty()) {
                if ($date->toDateString() < Carbon::today()->toDateString()) {
                    $shipmentDate = Carbon::today();
                    $order->update(['shipment_date' => $shipmentDate]);
                    if($order->task != null) {
                        $dateStart = new Carbon($order->task->taskTime->date_start);
                        $dateEnd = new Carbon($order->task->taskTime->date_end);
                        $order->task->taskTime->update([
                            'date_start' => $dateStart->addDay()->toDateTimeString(),
                            'date_end' => $dateEnd->addDay()->toDateTimeString(),
                        ]);
                    }
                }
            } else {
                foreach ($order->packages as $package) {
                    if($package->status == 'NEW') {
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
        }
    }
}
