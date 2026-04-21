<?php

namespace App\DTO\Schenker\Response;

use App\DTO\BaseDTO;

class GetOrderStatusResponseDTO extends BaseDTO
{

    private $result;
    private $status;
    private $description;
    private $errorNumber;
    private $errorDescription;

    public function __construct(string $result, int $status, string $description, string $errorNumber, string $errorDescription)
    {
        $this->result = $result;
        $this->status = $status;
        $this->description = $description;
        $this->errorNumber = $errorNumber;
        $this->errorDescription = $errorDescription;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getDescription(): string
    {
        return $this->description;
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
