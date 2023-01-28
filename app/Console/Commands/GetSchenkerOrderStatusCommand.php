<?php

namespace App\Console\Commands;

use App\DTO\Schenker\Request\GetOrderStatusRequestDTO;
use App\Entities\OrderPackage;
use App\Exceptions\SoapException;
use App\Exceptions\SoapParamsException;
use App\Services\SchenkerService;
use Illuminate\Console\Command;

/** TODO Check this after we have any order in system */
class GetSchenkerOrderStatusCommand extends Command
{
    protected $signature = 'schenker:get_order_status {orderPackageId}';

    protected $description = 'Pulling package tracking information from Schenker API';

    /**
     * @throws SoapException
     * @throws SoapParamsException
     * @throws Exception
     */
    public function handle()
    {
        $package = OrderPackage::where('id', $this->argument('orderPackageId'))->first();
        if ($package) {
            $getOrderStatusRequestDTO = new GetOrderStatusRequestDTO(config('integrations.schenker.client_id'), $package->sending_number);
            $getOrderStatusResponseDTO = SchenkerService::getOrderStatus($getOrderStatusRequestDTO);
            return true;
        }

        $this->error('Package ID not found in database');
        return false;
    }
}
