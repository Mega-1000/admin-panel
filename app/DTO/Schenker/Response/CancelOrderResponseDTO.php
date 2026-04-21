<?php

namespace App\DTO\Schenker\Response;

use App\DTO\BaseDTO;

class CancelOrderResponseDTO extends BaseDTO
{

    private $result;
    private $errorNumber;
    private $errorDescription;

    public function __construct(string $result, string $errorNumber, string $errorDescription)
    {
        $this->result = $result;
        $this->errorNumber = $errorNumber;
        $this->errorDescription = $errorDescription;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getErrorNumber(): string
    {
        return $this->errorNumber;
    }

    public function getErrorDescription(): string
    {
        return $this->errorDescription;
    }

}
