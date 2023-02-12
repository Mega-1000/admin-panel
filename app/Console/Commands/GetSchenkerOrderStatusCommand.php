<?php

namespace App\Console\Commands;

use App\DTO\Schenker\Request\GetOrderStatusRequestDTO;
use App\Entities\OrderPackage;
use App\Exceptions\SoapException;
use App\Exceptions\SoapParamsException;
use App\Services\SchenkerService;
use Exception;
use Illuminate\Console\Command;
use Throwable;

/** TODO Check this after we have any order in system */
class GetSchenkerOrderStatusCommand extends Command
{
    protected $signature = 'schenker:get_order_status {orderPackageId}';

    protected $description = 'Pulling package tracking information from Schenker API';

    /**
     * @throws SoapException
     * @throws SoapParamsException
     * @throws Exception
     * @throws Throwable
     */
    public function handle(): bool
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
