<?php

namespace App\DTO\Schenker\Request;

use App\DTO\BaseDTO;
use JsonSerializable;

class GetOrderDocumentRequestDTO extends BaseDTO implements JsonSerializable
{

    const DEFAULT_REFERENCE_TYPE = "DWB";
    const DEFAULT_RETURN_DOCUMENT_TYPE = "LP";

    private $clientId;
    private $referenceType;
    private $referenceNumber;
    private $type;

    public function __construct(
        string $clientId,
        string $referenceNumber,
        string $referenceType = self::DEFAULT_REFERENCE_TYPE,
        string $type = self::DEFAULT_RETURN_DOCUMENT_TYPE
    )
    {
        $this->clientId = $clientId;
        $this->referenceType = $referenceType;
        $this->referenceNumber = $referenceNumber;
        $this->type = $type;
    }

    public function jsonSerialize(): array
    {
        return [
            'clientId' => $this->clientId,
            'referenceType' => $this->referenceType,
            'referenceNumber' => $this->referenceNumber,
            'type' => $this->type,
        ];
    }
}
