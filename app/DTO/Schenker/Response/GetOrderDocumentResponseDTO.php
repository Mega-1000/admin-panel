<?php

namespace App\DTO\Schenker\Response;

use App\DTO\BaseDTO;

class GetOrderDocumentResponseDTO extends BaseDTO
{

    private $base64DocumentContent;

    public function __construct(?string $base64DocumentContent)
    {
        $this->base64DocumentContent = $base64DocumentContent;
    }

    public function getBase64DocumentContent(): ?string
    {
        return $this->base64DocumentContent;
    }

}
