<?php

namespace App\DTO\Schenker\Request;

use App\DTO\BaseDTO;
use App\DTO\Schenker\DangerProductDTO;
use App\DTO\Schenker\PackageDTO;
use App\DTO\Schenker\PayerDTO;
use App\DTO\Schenker\RecipientDTO;
use App\DTO\Schenker\ReferenceDTO;
use App\DTO\Schenker\SenderDTO;
use App\DTO\Schenker\ServiceDTO;
use App\DTO\Schenker\SsccDTO;
use App\Enums\Schenker\ProductType;
use Carbon\Carbon;
use JsonSerializable;

class GetOrderRequestDTO extends BaseDTO implements JsonSerializable
{

    protected $clientId;
    protected $installId;
    protected $dataOrigin;
    protected $waybillNo;
    protected $product;
    protected $pickupFrom;
    protected $pickupTo;
    protected $deliveryFrom;
    protected $deliveryTo;
    protected $comment;
    protected $deliveryInstructions;
    protected $senderDTO;
    protected $recipientDTO;
    protected $payerDTO;
    protected $packagesDTOs;
    protected $ssccMatching;
    protected $sscc;
    protected $dangerProductDTOs;
    protected $services;
    protected $references;

    /**
     * @param PackageDTO[] $packagesDTOs
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
            'pickupFrom' => $this->convertDate($this->pickupFrom),
            'pickupTo' => $this->convertDate($this->pickupTo),
            'comment' => $this->substrText($this->comment ?? '', 100),
            'sender' => $this->senderDTO,
            'recipient' => $this->recipientDTO,
            'payer' => $this->payerDTO,
            'packages' => $this->packagesDTOs,
        ];

        $this->deliveryInstructions = $this->substrText($this->deliveryInstructions ?? '', 128);
        $this->installId = $this->substrText($this->installId ?? '', 7);
        $this->waybillNo = $this->substrText($this->getOnlyNumbers($this->waybillNo ?? ''), 10);
        $this->deliveryFrom = $this->convertDate($this->deliveryFrom);
        $this->deliveryTo = $this->convertDate($this->deliveryTo);


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
            'deliveryInstructions' => 'deliveryInstructions',
        ];

        return array_merge($orderData, $this->getOptionalFilledFields());
    }
}
