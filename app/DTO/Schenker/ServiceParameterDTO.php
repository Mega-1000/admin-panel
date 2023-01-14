<?php

namespace App\DTO\Schenker;

use App\DTO\BaseDTO;

class ServiceParameterDTO extends BaseDTO
{
    private $mainParameter;
    private $documentType;
    private $documentNumber;

    /**
     * @param string|float $mainParameter
     */
    public function __construct($mainParameter, string $documentType, string $documentNumber)
    {
        $this->mainParameter = $mainParameter;
        $this->documentType = $documentType;
        $this->documentNumber = $documentNumber;
    }

    public function getMainParameterAsString(): string
    {
        return $this->substrText($this->mainParameter);
    }

    public function getMainParameterFromFloatToInt(): int
    {
        return $this->floatToInt($this->mainParameter);
    }

    public function getDocumentType(): string
    {
        return $this->substrText($this->documentType);
    }

    public function getDocumentNumber(): string
    {
        return $this->substrText($this->documentNumber);
    }

}
