<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Entities\ShipmentGroup;
use App\Enums\CourierName;
use App\Repositories\ShipmentGroupRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Save package group job
 */
class SavePackageGroupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var ShipmentGroupRepository
     */
    private $shipmentGroupRepository;

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
     */
    public function handle()
    {
        $this->shipmentGroupRepository = app(ShipmentGroupRepository::class);
        foreach ($this->order->packages as $package) {
            if (!empty($package->shipmentGroup)) {
                continue;
            }
            $searchCriteria = [
                'courier_name' => $package->service_courier_name,
                'shipment_date' => Carbon::now()->format('Y-m-d'),
            ];

            if ($package->service_courier_name === 'DPD') {
                if ($package->symbol === 'DPD_D_smart' || $package->symbol === 'DPD_d') {
                    $searchCriteria['package_type'] = 'DPD_D';
                } else {
                    $searchCriteria['package_type'] = 'DPD';
                }
            } elseif ($package->service_courier_name === 'POCZTEX') {
                if (strpos($package->symbol, 'P_')) {
                    $searchCriteria['package_type'] = 'POCZTEX_P';
                } else {
                    $searchCriteria['package_type'] = 'POCZTEX';
                }
            }
//jeśli ze wczoraja nie jest
            $shipmentGroups = $this->shipmentGroupRepository->findWhere($searchCriteria);
            $shipmentGroup = $shipmentGroups->filter(function (ShipmentGroup $shipmentGroup) {
                return $shipmentGroup->closed == false;
            })->first();
            if (empty($shipmentGroup)) {
                $searchCriteria['lp'] = count($shipmentGroups) + 1;
                $shipmentGroup = $this->shipmentGroupRepository->create($searchCriteria);
            }
            $shipmentGroup->packages()->save($package);
        }
    }
}
