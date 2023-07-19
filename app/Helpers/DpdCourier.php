<?php

namespace App\Helpers;

use App\Entities\OrderPackage;
use App\Enums\CourierStatus\DpdPackageStatus;
use App\Enums\PackageStatus;
use App\Helpers\interfaces\ICourier;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Request;

class DpdCourier implements ICourier
{
    use CourierTrait;

    /**
     * @throws GuzzleException
     */
    public function checkStatus(OrderPackage $package): void
    {
        $params = [
            'q' => $package->letter_number,
            'typ' => 1
        ];

        $options = ['form_params' => $params];
        $response = $this
            ->prepareConnectionForTrackingStatus($this->config['dpd']['tracking_url'], Request::METHOD_POST, $options)
            ->getBody()
            ->getContents();

        $packageStatusRegex = '/(' . DpdPackageStatus::getDescription(DpdPackageStatus::DELIVERED)
            . ')|(' . DpdPackageStatus::getDescription(DpdPackageStatus::SENDING)
            . ')|(' . DpdPackageStatus::getDescription(DpdPackageStatus::INDELIVERY)
            . ')|(' . DpdPackageStatus::getDescription(DpdPackageStatus::INWAREHOUSE)
            . ')|(' . DpdPackageStatus::getDescription(DpdPackageStatus::WAITING_FOR_SENDING) . ')/';

        preg_match($packageStatusRegex, $response, $matches);

        if (!empty($matches)) {
            switch ($matches[0]) {
                case DpdPackageStatus::getDescription(DpdPackageStatus::DELIVERED):
                    $package->status = PackageStatus::DELIVERED;
                    break;
                case DpdPackageStatus::getDescription(DpdPackageStatus::SENDING):
                case DpdPackageStatus::getDescription(DpdPackageStatus::INDELIVERY):
                case DpdPackageStatus::getDescription(DpdPackageStatus::INWAREHOUSE):
                    $package->status = PackageStatus::SENDING;
                    break;
                case DpdPackageStatus::getDescription(DpdPackageStatus::WAITING_FOR_SENDING):
                    $package->status = PackageStatus::WAITING_FOR_SENDING;
                    break;
            }

            $package->save();
        }
    }
}
