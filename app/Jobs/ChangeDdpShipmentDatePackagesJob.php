<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Entities\OrderPackage;
use App\Entities\PackageTemplate;
use App\Helpers\OrderPackagesDataHelper;
use App\Helpers\Package;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ChangeDdpShipmentDatePackagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * @var OrderPackagesDataHelper
     */
    private $orderPackagesDataHelper;

    /**
     * Create a new job instance.
     *
     * @return void
     */
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
        $packages = OrderPackage::whereDate('shipment_date', '<=', Carbon::today())->where('service_courier_name', Package::DPD_COURIER)->where('status', PackageTemplate::STATUS_NEW)->get();
        $packages->map(function ($pack) {
            $shipmentDate = Carbon::tomorrow();
            Log::notice('Tutaj zmieniamy date paczek ale tylko dpd ' . $pack->shipment_date->format('Y-m-d H:i:s'));

            $pack->shipment_date = $shipmentDate;
            $pack->delivery_date = $this->orderPackagesDataHelper->calculateDeliveryDate($shipmentDate);
            $pack->save();
        });
    }
}
