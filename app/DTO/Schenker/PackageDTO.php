<?php

namespace App\DTO\Schenker;

use App\DTO\BaseDTO;
use App\Services\SchenkerService;
use JsonSerializable;

class PackageDTO extends BaseDTO implements JsonSerializable
{

    protected $packageId;
    protected $name;
    protected $packageCode;
    protected $quantity;
    protected $protection;
    protected $weight;
    protected $volume;
    protected $width;
    protected $length;
    protected $height;
    protected $stack;
    protected $notStandard;
    protected $notStandardComment;

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
        bool    $notStandard,
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
            'name' => $this->substrText($this->name, 20),
            'packCode' => SchenkerService::searchPackageCode($this->packageCode),
            'quantity' => $this->quantity,
            'protection' => $this->substrText($this->protection, 30),
            'weight' => $this->floatToInt($this->weight),
            'volume' => $this->floatToInt($this->volume),
            'width' => $this->floatToInt($this->volume),
            'length' => $this->floatToInt($this->length),
            'height' => $this->floatToInt($this->height)
        ];

        $this->optionalFields = [
            'colliId' => 'packageId',
            'stack' => 'stack',
        ];

        if ($this->notStandard === true) {
            $this->optionalFields['nstReason'] = $this->notStandardComment;
        }

        return array_merge($packageData, $this->getOptionalFilledFields());
    }


}
