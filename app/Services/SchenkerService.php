<?php

namespace App\Services;

use App\DTO\Schenker\Request\CancelOrderRequestDTO;
use App\DTO\Schenker\Request\GetOrderDocumentDTO;
use App\DTO\Schenker\Request\GetOrderDocumentRequestDTO;
use App\DTO\Schenker\Request\GetTrackingRequestDTO;
use App\DTO\Schenker\Request\OrderRequestDTO;
use App\DTO\Schenker\Response\CancelOrderResponseDTO;
use App\DTO\Schenker\Response\CreateOrderResponseDTO;
use App\DTO\Schenker\Response\GetOrderDocumentResponseDTO;
use App\DTO\Schenker\Response\GetOrderStatusResponseDTO;
use App\DTO\Schenker\Response\GetTrackingResponseDTO;
use App\Exceptions\SapException;
use App\Exceptions\SoapParamsException;
use App\Utils\SoapParams;
use Illuminate\Support\Facades\Storage;
use JsonSerializable;

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
    private static function prepareAndSendRequest(string $paramName, ?JsonSerializable $dataObject, string $action): array
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
     * @throws SoapParamsException|SapException
     */
    public static function createNewOrder(OrderRequestDTO $schenkerOrderDTO): CreateOrderResponseDTO
    {
        $response = self::prepareAndSendRequest(
            'createOrderRequest',
            $schenkerOrderDTO,
            'createOrder'
        );

        return new CreateOrderResponseDTO(
            $response['statusCode'],
            $response['orderId']
        );
    }

    /**
     * @throws SoapParamsException
     * @throws SapException
     */
    public static function getOrderStatus(GetOrderStatusResponseDTO $getOrderStatusResponseDTO): GetOrderStatusResponseDTO
    {
        $response = self::prepareAndSendRequest(
            'getOrderStatusRequest',
            $getOrderStatusResponseDTO,
            'getOrderStatus'
        );

        return new GetOrderStatusResponseDTO(
            $response['result'] ?? '',
            $response['pcStatus'] ?? 0,
            $response['pcOpis'] ?? '',
            $response['piError'] ?? '',
            $response['pcError'] ?? ''
        );
    }

    /**
     * @return GetTrackingResponseDTO[]
     * @throws SapException
     *
     * @throws SoapParamsException
     */
    public static function getGetTrackingInformation(GetTrackingRequestDTO $getTrackingRequestDTO): array
    {
        $response = self::prepareAndSendRequest(
            'getTrackingRequest',
            $getTrackingRequestDTO,
            'getTracking'
        );

        $trackingResponseDTO = [];
        if (array_key_exists('consignment', $response) && $response['consignment'] !== null) {
            foreach (($response['consignment']['eventList'] ?? []) as $event) {
                $eventData = $event['event'];
                $trackingResponseDTO[] = new GetTrackingResponseDTO(
                    $eventData['seq'] ?? 1,
                    $eventData['eventDesc'] ?? '',
                    $eventData['eventType'] ?? '',
                    $eventData['eventCode'] ?? ''
                );
            }
        }

        return $trackingResponseDTO;
    }

    public static function cancelOrder(CancelOrderRequestDTO $cancelOrderRequestDTO): CancelOrderResponseDTO
    {
        $response = self::prepareAndSendRequest(
            'cancelOrderRequest',
            $cancelOrderRequestDTO,
            'cancelOrder'
        );

        return new CancelOrderResponseDTO(
            $response['result'],
            $response['iError'],
            $response['cError']
        );
    }

    /**
     * @throws SoapParamsException
     * @throws SapException
     */
    public static function getDocument(GetOrderDocumentRequestDTO $getOrderDocumentRequestDTO): GetOrderDocumentResponseDTO
    {
        $response = self::prepareAndSendRequest(
            'getDocumentsRequest',
            $getOrderDocumentRequestDTO,
            'getDocuments'
        );

        return new GetOrderDocumentResponseDTO($response['document'] ?? '');
    }

}
