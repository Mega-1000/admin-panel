<?php

namespace App\Services;

use App\DTO\Schenker\Request\OrderDTO;
use App\Exceptions\SapException;
use App\Exceptions\SoapParamsException;
use App\Utils\SoapParams;
use Illuminate\Support\Facades\Storage;

class SchenkerService extends SoapClientService
{

    /**
     * @throws SoapParamsException
     * @throws SapException
     */
    public static function getPackageDictionary(): array
    {
        return self::prepareAndSendRequest(
            'getPackageDictionaryRequest',
            null,
            'getPackageDictionary'
        );
    }

    /**
     * @throws SoapParamsException
     * @throws SapException
     */
    private static function prepareAndSendRequest(string $paramName, ?BaseDTO $dataObject, string $action): array
    {
        $soapParams = new SoapParams();
        $soapParams->setParamDTOObject($paramName, $dataObject);
        return self::sendRequest(
            Storage::disk('wsdl')->path('schenker.wsdl'),
            $action,
            $soapParams
        );
    }

    /**
     * @throws SoapParamsException
     * @throws SapException
     */
    public static function createNewOrder(OrderDTO $schenkerOrderDTO): array
    {
        return self::prepareAndSendRequest(
            'createOrderRequest',
            $schenkerOrderDTO,
            'createOrder'
        );
    }

    /**
     * @throws SoapParamsException
     * @throws SapException
     */
    public static function getOrderStatus(OrderStatusDTO $schenkerOrderStatusDTO): array
    {
        return self::prepareAndSendRequest(
            'getOrderStatusRequest',
            $schenkerOrderStatusDTO,
            'getOrderStatus'
        );
    }

    /**
     * @throws SoapParamsException
     * @throws SapException
     */
    public static function getGetTrackingInformation(OrderTrackingDTO $schenkerOrderTrackingDTO): array
    {
        return self::prepareAndSendRequest(
            'getTrackingRequest',
            $schenkerOrderTrackingDTO,
            'getTracking'
        );
    }

    /**
     * @throws SoapParamsException
     * @throws SapException
     */
    public static function getDocument(OrderDocumentDTO $schenkerOrderDocumentDTO): array
    {
        return self::prepareAndSendRequest(
            'getDocumentsRequest',
            $schenkerOrderDocumentDTO,
            'getDocuments'
        );
    }

}
