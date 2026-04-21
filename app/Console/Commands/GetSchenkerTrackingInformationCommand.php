<?php

namespace App\Console\Commands;

use App\DTO\Schenker\Request\GetTrackingRequestDTO;
use App\Entities\OrderPackage;
use App\Exceptions\SoapException;
use App\Exceptions\SoapParamsException;
use App\Services\SchenkerService;
use Exception;
use Illuminate\Console\Command;
use Throwable;

/** TODO Check after we will have any order in progress */
class GetSchenkerTrackingInformationCommand extends Command
{
    protected $signature = 'schenker:get_tracking_information {orderPackageId}';

    protected $description = 'Pulling package tracking information from Schenker API';

    /**
     * @throws SoapException
     * @throws SoapParamsException
     * @throws Exception
     * @throws Throwable
     */
    public function handle()
    {
        $package = OrderPackage::where('id', $this->argument('orderPackageId'))->first();
        if ($package) {
            $getTrackingRequestDTO = new GetTrackingRequestDTO($package->sending_number);
            $getTrackingResponseDTO = SchenkerService::getGetTrackingInformation($getTrackingRequestDTO);

            dd($getTrackingResponseDTO);
            return true;
        }

        $this->error('Package ID not found in database');
        return false;
    }
}
