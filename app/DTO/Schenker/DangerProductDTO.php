<?php

namespace App\DTO\Schenker;

use App\DTO\BaseDTO;
use App\Enums\Schenker\DangerProductPackageType;
use App\Enums\Schenker\DangerProductRiskLevel;
use JsonSerializable;

class DangerProductDTO extends BaseDTO implements JsonSerializable
{

    private $uniqueNumber;
    private $riskLevel;
    private $weight;
    private $packagesQuantity;
    private $packageType;
    private $excluded;
    private $comment;
    private $technicalName;

    /**
     * @param string $uniqueNumber
     * @param ?string $riskLevel
     * @param float $weight
     * @param float $packagesQuantity
     * @param float $packageType
     * @param bool $excluded
     * @param ?string $comment
     * @param ?string $technicalName
     */
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
            'adrUn' => substr($this->uniqueNumber, 0, 4),
            'adrWeight' => round($this->weight * 100),
            'adrColli' => $this->packagesQuantity,
        ];

        if (!DangerProductPackageType::checkIfTypeExists($this->packageType)) {
            throw new SchenkerException('Not existing package type was try to be set: ' . $this->packageType, 500);
        }

        $dangerProductData['adrPack'] = $this->packageType;

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
