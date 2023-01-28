<?php

namespace App\DTO\Schenker;

use App\DTO\BaseDTO;
use JsonSerializable;

class SsccDTO extends BaseDTO implements JsonSerializable
{

    private $packageNumber;
    private $packageSerialNumber;

    public function __construct(int $packageNumber, string $packageSerialNumber)
    {
        $this->packageNumber = $packageNumber;
        $this->packageSerialNumber = $packageSerialNumber;
    }

    public function jsonSerialize()
    {
        return [
            'colliId' => $this->getOnlyNumbers($this->packageNumber),
            'ssccNo' => $this->substrText($this->getOnlyNumbers($this->packageSerialNumber), 18),
        ];
    }

}
