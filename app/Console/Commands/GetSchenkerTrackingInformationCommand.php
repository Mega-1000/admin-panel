<?php

namespace App\Console\Commands;

use App\DTO\Schenker\Request\GetTrackingRequestDTO;
use App\Entities\OrderPackage;
use App\Exceptions\SoapException;
use App\Exceptions\SoapParamsException;
use App\Services\SchenkerService;
use Illuminate\Console\Command;

/** TODO Check after we will have any order in progress */
class GetSchenkerTrackingInformationCommand extends Command
{
    protected $signature = 'schenker:get_tracking_information {orderPackageId}';

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
            $getTrackingRequestDTO = new GetTrackingRequestDTO($package->sending_number);
            $getTrackingResponseDTO = SchenkerService::getGetTrackingInformation($getTrackingRequestDTO);

            dd($getTrackingResponseDTO);
            return true;
        }

        $this->error('Package ID not found in database');
        return false;
    }
}
