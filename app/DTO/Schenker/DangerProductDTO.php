<?php

namespace App\DTO\Schenker;

use App\DTO\BaseDTO;
use App\Enums\Schenker\DangerProductPackageType;
use App\Enums\Schenker\DangerProductRiskLevel;
use JsonSerializable;

class DangerProductDTO extends BaseDTO implements JsonSerializable
{

    protected $uniqueNumber;
    protected $riskLevel;
    protected $weight;
    protected $packagesQuantity;
    protected $packageType;
    protected $excluded;
    protected $comment;
    protected $technicalName;

    public function __construct(
        string  $uniqueNumber,
        ?string $riskLevel,
        float   $weight,
        float   $packagesQuantity,
        float   $packageType,
        bool    $excluded,
        ?string $comment,
        ?string $technicalName
    )
    {
        $this->uniqueNumber = $uniqueNumber;
        $this->riskLevel = $riskLevel;
        $this->weight = $weight;
        $this->packagesQuantity = $packagesQuantity;
        $this->packageType = $packageType;
        $this->excluded = $excluded;
        $this->comment = $comment;
        $this->technicalName = $technicalName;
    }

    public function jsonSerialize()
    {
        $dangerProductData = [
            'adrUn' => $this->substrText($this->uniqueNumber, 4),
            'adrWeight' => $this->floatToInt($this->weight),
            'adrColli' => $this->packagesQuantity,
            'adrLq' => $this->excluded,
            'adrNotes' => $this->substrText($this->comment, 150),
        ];

        if (!DangerProductPackageType::checkIfTypeExists($this->packageType)) {
            throw new SchenkerException('Not existing package type was try to be set: ' . $this->packageType, 500);
        }

        $dangerProductData['adrPack'] = $this->packageType;

        $this->comment = $this->substrText($this->comment ?? '', 150);
        $this->technicalName = $this->substrText($this->technicalName ?? '', 255);

        $this->optionalFields = [
            'adrNotes' => 'comment',
            'adrTechName' => 'technicalName',
        ];

        if (DangerProductRiskLevel::checkRiskLevelExists($this->riskLevel)) {
            $this->optionalFields['adrGroup'] = $this->riskLevel;
        }

        return array_merge($dangerProductData, $this->getOptionalFilledFields());
    }


}
