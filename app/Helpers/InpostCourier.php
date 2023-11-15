<?php

namespace App\Helpers;

use App\Entities\OrderPackage;
use App\Enums\CourierStatus\InpostPackageStatus;
use App\Enums\PackageStatus;
use App\Helpers\interfaces\iCourier;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use OutOfRangeException;
use Symfony\Component\HttpFoundation\Request;

class InpostCourier implements iCourier
{
    use CourierTrait;

    /**
     * @throws GuzzleException
     */
    public function checkStatus(OrderPackage $package): void
    {
        $url = $this->config['inpost']['tracking_url'] . $package->letter_number;
        $params = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->config['inpost']['authorization']
            ],
        ];

        $response = $this->prepareConnectionForTrackingStatus($url, Request::METHOD_GET, $params);
        $result = json_decode((string)$response->getBody(), true);

        if (!isset($result['items'][0])) {
            Log::info('Something went wrong with package:', ['order_id' => $package->order_id, 'package_id' => $package->id]);
            return;
        }

        try {
            $packageStatus = $result['items'][0]['status'];
            switch (true) {
                case in_array($packageStatus, InpostPackageStatus::DELIVERED):
                    $package->status = PackageStatus::DELIVERED;
                    break;
                case in_array($packageStatus, InpostPackageStatus::SENDING):
                    $package->status = PackageStatus::SENDING;
                    break;
                case in_array($packageStatus, InpostPackageStatus::WAITING_FOR_SENDING):
                    $package->status = PackageStatus::WAITING_FOR_SENDING;
                    break;
            }
            $package->save();
        } catch (OutOfRangeException $e) {
            Log::info('Something went wrong with package:', ['order_id' => $package->order_id, 'package_id' => $package->id]);
        }
    }
}
