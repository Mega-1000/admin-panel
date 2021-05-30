<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Entities\OrderPackage;
use App\Helpers\OrderPackagesDataHelper;
use App\Repositories\OrderPackageRepository;
use App\Repositories\OrderRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ChangeShipmentDatePackagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    protected $orderPackageRepository;

    protected $orderPackagesDataHelper;

    protected $orderRepository;

    public function __construct()
    {
        $this->orderPackagesDataHelper = new OrderPackagesDataHelper();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orders = Order::whereDate('shipment_date', '<', Carbon::today())->get();
        foreach ($orders as $order) {
            $date = new Carbon($order->shipment_date);
            if ($order->hasLabel(66)) {
                continue;
            }
            if ($order->packages->isEmpty()) {
                if ($date->toDateString() < Carbon::today()->toDateString()) {
                    $shipmentDate = Carbon::today();
                    $order->update(['shipment_date' => $shipmentDate]);
                    if ($order->task != null) {
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
                    if ($package->status != 'NEW') {
                        continue;
                    }

                    if ($date->toDateString() >= Carbon::today()->toDateString()) {
                        continue;
                    }

                    $shipmentDate = Carbon::today();
                    $order->shipment_date = $shipmentDate;
                    $package->shipment_date = $shipmentDate;
                    $package->delivery_date = $this->orderPackagesDataHelper->calculateDeliveryDate($shipmentDate);
                    if (empty($package->order)) {
                        continue;
                    }
                    $package->order->shipment_date = $shipmentDate;
                    $package->save();
                    if (empty($package->order->task)) {
                        continue;
                    }
                    $dateStart = new Carbon($package->order->task->taskTime->date_start);
                    $dateEnd = new Carbon($package->order->task->taskTime->date_end);
                    $package->order->task->taskTime->update([
                        'date_start' => $dateStart->addDay()->toDateTimeString(),
                        'date_end' => $dateEnd->addDay()->toDateTimeString(),
                    ]);
                }
                $order->save();
            }
        }
    }
}
