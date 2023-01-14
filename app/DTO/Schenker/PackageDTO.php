<?php

namespace App\DTO\Schenker;

use App\DTO\BaseDTO;
use JsonSerializable;

class PackageDTO extends BaseDTO implements JsonSerializable
{

    private $packageId;
    private $name;
    private $packageCode;
    private $quantity;
    private $protection;
    private $weight;
    private $volume;
    private $width;
    private $length;
    private $height;
    private $stack;
    private $notStandard;
    private $notStandardComment;

    /**
     * @param ?string $packageId
     * @param string $name
     * @param string $packageCode
     * @param float $quantity
     * @param string $protection
     * @param float $weight
     * @param float $volume
     * @param float $width
     * @param float $length
     * @param float $height
     * @param ?bool $stack
     * @param ?bool $notStandard
     * @param ?string $notStandardComment
     */
    public function __construct(
        ?string $packageId,
        string  $name,
        string  $packageCode,
        float   $quantity,
        string  $protection,
        float   $weight,
        float   $volume,
        float   $width,
        float   $length,
        float   $height,
        ?bool   $stack,
        ?bool   $notStandard,
        ?string $notStandardComment
    )
    {
        $this->packageId = $packageId;
        $this->name = $name;
        $this->packageCode = $packageCode;
        $this->quantity = $quantity;
        $this->protection = $protection;
        $this->weight = $weight;
        $this->volume = $volume;
        $this->width = $width;
        $this->length = $length;
        $this->height = $height;
        $this->stack = $stack;
        $this->notStandard = $notStandard;
        $this->notStandardComment = $notStandardComment;
    }

    public function jsonSerialize(): array
    {
        $packageData = [
            'name' => substr($this->name, 0, 20),
            'packCode' => substr($this->packageCode, 0, 20),
            'quantity' => $this->quantity,
            'protection' => substr($this->protection, 0, 30),
            'weight' => round($this->weight * 100),
            'volume' => round($this->volume * 100),
            'width' => round($this->volume * 100),
            'length' => round($this->length * 100),
            'height' => rund($this->height * 100)
        ];

        $this->optionalFields = [
            'colliId' => 'packageId',
            'stack' => 'stack',
            'notStandard' => 'notStandard',
        ];

        if ($this->notStandard === true) {
            $this->optionalFields['nstReason'] = $this->notStandardComment;
        }

        return array_merge($packageData, $this->getOptionalFilledFields());
    }


}
