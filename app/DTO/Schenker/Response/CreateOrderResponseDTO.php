<?php

namespace App\DTO\Schenker\Response;

use App\DTO\BaseDTO;

class CreateOrderResponseDTO extends BaseDTO
{
    const STATUS_CODE_OK = 'OK';
    const STATUS_CODE_ERROR = 'ERROR';

    private $statusCode;
    private $orderId;

    public function __construct(
        string $statusCode,
        string $orderId
    )
    {
        $this->statusCode = $statusCode;
        $this->orderId = $orderId;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

}
