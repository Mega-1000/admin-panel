<?php

namespace App\Helpers;

use App\Entities\OrderPackage;
use App\Enums\CourierStatus\GlsPackageStatus;
use App\Enums\PackageStatus;
use App\Helpers\interfaces\iCourier;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;

class GlsCourier implements iCourier
{
    use CourierTrait;

    /**
     * @throws GuzzleException
     */
    public function checkStatus(OrderPackage $package): void
    {
        $url = $this->config['gls']['tracking_url'] . $package->letter_number;
        try {
            $response = json_decode(
                $this->prepareConnectionForTrackingStatus($url, Request::METHOD_GET, [])
                    ->getBody()
                    ->getContents()
            );

            if (empty($response->tuStatus)) {
                Log::notice('Brak odpowiedzi z statusem paczki nr: ' . $package->letter_number);
                return;
            }

            $packageStatus = $response->tuStatus[0]->progressBar->statusInfo;

            switch ($packageStatus) {
                case GlsPackageStatus::DELIVERED:
                    $package->status = PackageStatus::DELIVERED;
                    break;
                case GlsPackageStatus::INTRANSIT:
                case GlsPackageStatus::INWAREHOUSE:
                case GlsPackageStatus::INDELIVERY:
                case GlsPackageStatus::PREADVICE:
                    $package->status = PackageStatus::SENDING;
                    break;
            }

            $package->save();
        } catch (RequestException $e) {
            Log::error('Wystąpił problem przy sprawdzaniu statusu paczki: ' . $package->letter_number . ' Błąd: ' . $e->getMessage());
        }
    }
}
