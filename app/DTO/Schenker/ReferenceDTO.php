<?php

namespace App\DTO\Schenker;

use App\DTO\BaseDTO;
use JsonSerializable;

class ReferenceDTO extends BaseDTO implements JsonSerializable
{

    private $type;
    private $number;

    public function __construct(int $type, string $number)
    {
        $this->type = $type;
        $this->number = $number;
    }


    public function jsonSerialize(): array
    {
        return [
            'refType' => $this->getOnlyNumbers($this->type),
            'refNo' => $this->substrText($this->number),
        ];
    }

}
