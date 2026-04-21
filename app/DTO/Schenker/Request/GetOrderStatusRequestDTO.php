<?php

namespace App\DTO\Schenker\Request;

use App\DTO\BaseDTO;
use JsonSerializable;

class GetOrderStatusRequestDTO extends BaseDTO implements JsonSerializable
{

    const REF_TYPE_WAYBILL = 'DWB';
    const REF_TYPE_CLIENT_ORDER_REF_NUMBER = 'COR';

    private $clientId;
    private $referenceType;
    private $referenceNumber;

    public function __construct(string $clientId, string $referenceNumber, string $referenceType = 'DWB')
    {
        $this->clientId = $clientId;
        $this->referenceType = $referenceType;
        $this->referenceNumber = $referenceNumber;
    }

    public function jsonSerialize(): array
    {
        return [
            'clientId' => $this->clientId,
            'pcReference_type' => $this->referenceType,
            'pcReference_number' => $this->referenceNumber,
        ];
    }

}
