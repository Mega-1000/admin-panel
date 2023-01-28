<?php

namespace App\Factory\Schenker;

use App\DTO\Schenker\ServiceDTO;
use App\DTO\Schenker\ServiceParameterDTO;
use App\Enums\Schenker\SupportedService;
use App\Providers\Schenker\SchenkerServiceProvider;
use Illuminate\Support\Facades\App;

class ServiceFactory
{

    /**
     * @param string|int $mainParameter
     */
    public static function getServiceParameters($mainParameter, string $documentType = '', string $documentNumber = ''): ServiceParameterDTO
    {
        return App::make(SchenkerServiceProvider::DTO_SERVICE_PARAMETERS, [
            'mainParameter' => $mainParameter,
            'documentType' => $documentType,
            'documentNumber' => $documentNumber,
        ]);
    }

    /**
     * @param string $servicesSeparatedByComma - services separated by comma, example: "1, 2, 92" (chars other than "," and numbers are filtered from string)
     * @param ServiceParameters[] $servicesParameters - generated from getServiceParameter method with key as service number
     * @return ServiceDTO[]
     */
    public static function getServicesFromString(string $servicesSeparatedByComma, array $servicesParameters): array
    {
        $servicesString = preg_replace('/[^\d\,]+/', '', $servicesSeparatedByComma);
        $servicesArray = explode(',', $servicesString);
        $filteredEmptyServices = array_filter($servicesArray);
        
        $defaultServices = SupportedService::getDefaultServices();

        $uniqueServices = array_values(array_unique(array_merge($filteredEmptyServices, $defaultServices)));

        $servicesDTOs = ['service' => []];

        if (count($uniqueServices) > 0) {
            foreach ($uniqueServices as $uniqueService) {
                $serviceDTO = self::getServiceDTO($uniqueService, array_key_exists($uniqueService, $servicesParameters) ? $servicesParameters[$uniqueService] : null);
                if ($serviceDTO !== null) {
                    $servicesDTOs['service'][] = $serviceDTO;
                }
            }
        }

        return $servicesDTOs;
    }

    public static function getServiceDTO(int $serviceNumber, ?ServiceParameterDTO $serviceParameterDTO = null): ?ServiceDTO
    {
        if (SupportedService::isSupportedService($serviceNumber)) {

            switch ($serviceNumber) {
                case SupportedService::TYPE_UNLOADING_OF_CARGO:
                    return self::createUnloadingServiceDTO();
                case SupportedService::TYPE_PAYMENT_ON_DELIVERY:
                    return self::createPaymentOnDeliveryServiceDTO($serviceParameterDTO);
                case SupportedService::TYPE_PHONE_NOTIFICATIONS:
                    return self::createPhoneNotificationServiceDTO($serviceParameterDTO);
                case SupportedService::TYPE_EMAIL_NOTIFICATIONS:
                    return self::createEmailNotificationServiceDTO($serviceParameterDTO);
            }

        }

        return null;
    }

    private static function createUnloadingServiceDTO(): ServiceDTO
    {
        return App::make(SchenkerServiceProvider::DTO_SERVICE, [
            'code' => SupportedService::TYPE_UNLOADING_OF_CARGO,
        ]);
    }

    private static function createPaymentOnDeliveryServiceDTO(?ServiceParameterDTO $serviceParameterDTO): ?ServiceDTO
    {
        if ($serviceParameterDTO === null) {
            return null;
        }
        return App::make(SchenkerServiceProvider::DTO_SERVICE, [
            'code' => SupportedService::TYPE_PAYMENT_ON_DELIVERY,
            'mainParameter' => $serviceParameterDTO->getMainParameterFromFloatToInt(),
        ]);
    }

    private static function createPhoneNotificationServiceDTO(?ServiceParameterDTO $serviceParameterDTO): ?ServiceDTO
    {
        return App::make(SchenkerServiceProvider::DTO_SERVICE, [
            'code' => SupportedService::TYPE_PHONE_NOTIFICATIONS,
            'mainParameter' => $serviceParameterDTO === null ? null : $serviceParameterDTO->getMainParameterAsString(),
        ]);
    }

    private static function createEmailNotificationServiceDTO(?ServiceParameterDTO $serviceParameterDTO): ?ServiceDTO
    {
        return App::make(SchenkerServiceProvider::DTO_SERVICE, [
            'code' => SupportedService::TYPE_EMAIL_NOTIFICATIONS,
            'mainParameter' => $serviceParameterDTO === null ? null : $serviceParameterDTO->getMainParameterAsString(),
        ]);
    }

}
