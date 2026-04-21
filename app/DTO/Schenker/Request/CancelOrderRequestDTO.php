<?php

namespace App\DTO\Schenker\Request;

use App\DTO\BaseDTO;
use JsonSerializable;

class CancelOrderRequestDTO extends BaseDTO implements JsonSerializable
{

    private $clientId;
    private $orderId;

    public function __construct(string $clientId, string $orderId)
    {
        $this->clientId = $clientId;
        $this->orderId = $orderId;
    }

    public function jsonSerialize(): array
    {
        return [
            'clientId' => $this->clientId,
            'orderId' => $this->orderId,
        ];
    }


}
