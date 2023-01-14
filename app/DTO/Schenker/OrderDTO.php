<?php

namespace App\DTO\Schenker;

use App\DTO\BaseDTO;
use App\Enums\Schenker\ProductType;
use Carbon\Carbon;
use JsonSerializable;

class OrderDTO extends BaseDTO implements JsonSerializable
{

    private $clientId;
    private $installId;
    private $dataOrigin;
    private $waybillNo;
    private $product;
    private $pickupFrom;
    private $pickupTo;
    private $deliveryFrom;
    private $deliveryTo;
    private $comment;
    private $deliveryInstructions;
    private $senderDTO;
    private $recipientDTO;
    private $payerDTO;
    private $packagesDTOs;
    private $ssccMatching;
    private $sscc;
    private $dangerProductDTOs;
    private $services;
    private $references;

    /**
     * @param string $clientId
     * @param ?string $installId
     * @param ?string $dataOrigin
     * @param ?string $waybillNo
     * @param string $product
     * @param Carbon $pickupFrom
     * @param Carbon $pickupTo
     * @param ?Carbon $deliveryFrom
     * @param ?Carbon $deliveryTo
     * @param ?string $comment
     * @param ?string $deliveryInstructions
     * @param SenderDTO $senderDTO
     * @param RecipientDTO $recipientDTO
     * @param PayerDTO $payerDTO
     * @param PackageDTO[] $packagesDTOs
     * @param bool $ssccMatching
     * @param ?SsccDTO[] $sscc
     * @param ?DangerProductDTO[] $dangerProductDTOs
     * @param ?ServiceDTO[] $services
     * @param ?ReferenceDTO[] $references
     */
    public function __construct(
        string       $clientId,
        ?string      $installId,
        ?string      $dataOrigin,
        ?string      $waybillNo,
        string       $product,
        Carbon       $pickupFrom,
        Carbon       $pickupTo,
        ?Carbon      $deliveryFrom,
        ?Carbon      $deliveryTo,
        ?string      $comment,
        ?string      $deliveryInstructions,
        SenderDTO    $senderDTO,
        RecipientDTO $recipientDTO,
        PayerDTO     $payerDTO,
        array        $packagesDTOs,
        bool         $ssccMatching,
        ?array       $sscc,
        ?array       $dangerProductDTOs,
        ?array       $services,
        ?array       $references
    )
    {
        $this->clientId = $clientId;
        $this->installId = $installId;
        $this->dataOrigin = $dataOrigin;
        $this->waybillNo = $waybillNo;
        $this->product = $product;
        $this->pickupFrom = $pickupFrom;
        $this->pickupTo = $pickupTo;
        $this->deliveryFrom = $deliveryFrom;
        $this->deliveryTo = $deliveryTo;
        $this->comment = $comment;
        $this->deliveryInstructions = $deliveryInstructions;
        $this->senderDTO = $senderDTO;
        $this->recipientDTO = $recipientDTO;
        $this->payerDTO = $payerDTO;
        $this->packagesDTOs = $packagesDTOs;
        $this->ssccMatching = $ssccMatching;
        $this->sscc = $sscc;
        $this->dangerProductDTOs = $dangerProductDTOs;
        $this->services = $services;
        $this->references = $references;
    }


    public function jsonSerialize()
    {
        $orderData = [
            'clientId' => $this->clientId,
            'product' => ProductType::checkProductTypeExists($this->product) ? $this->product : ProductType::getDefaultType(),
            'pickupFrom' => $this->pickupFrom->format(config('shippings.providers.schenker.default_date_time_format', 'Y-m-dTH:i:s')),
            'pickupTo' => $this->pickupTo->format(config('shippings.providers.schenker.default_date_time_format', 'Y-m-dTH:i:s')),
            'comment' => substr($this->comment ?? '', 0, 100),
            'deliveryInstructions' => substr($this->deliveryInstructions ?? '', 0, 128),
            'sender' => $this->senderDTO,
            'recipient' => $this->recipientDTO,
            'payer' => $this->payerDTO,
            'packages' => $this->packagesDTOs,
        ];
        $this->optionalFields = [
            'installId' => 'installId',
            'dataOrigin' => 'dataOrigin',
            'waybillNo' => 'waybillNo',
            'deliveryFrom' => 'deliveryFrom',
            'deliveryTo' => 'deliveryTo',
            'ssccMatching' => 'ssccMatching',
            'Sscc' => 'sscc',
            'adrs' => 'dangerProductDTOs',
            'services' => 'services',
            'references' => 'references',
        ];

        return array_merge($orderData, $this->getOptionalFilledFields());
    }
}
