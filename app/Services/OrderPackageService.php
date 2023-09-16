<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Schenker\Request\CancelOrderRequestDTO;
use App\Entities\OrderPackage;
use App\Enums\CourierName;
use App\Enums\PackageStatus;
use App\Repositories\OrderPackageRepository;

class OrderPackageService
{
    const RESPONSE_OK = 'ok';

    public static function setPackageAsCancelled(OrderPackage $orderPackage): string
    {
        $orderPackage->status = PackageStatus::CANCELLED;
        if ($orderPackage->delivery_courier_name === CourierName::DB_SCHENKER) {
            return self::schnkerPackageCancell($orderPackage->sending_number);
        }

        $orderPackage->save();

        return self::RESPONSE_OK;
    }

    private static function schnkerPackageCancell(string $sendingNumber): string
    {
        $schenkerCancelOrderRequestDTO = new CancelOrderRequestDTO(
            config('integrations.schenker.client_id'),
            $sendingNumber
        );

        $cancelOrderResponseDTO = SchenkerService::cancelOrder($schenkerCancelOrderRequestDTO);

        if ($cancelOrderResponseDTO->getErrorNumber() > 0) {

            return $cancelOrderResponseDTO->getErrorDescription();
        }

        return self::RESPONSE_OK;
    }


}
