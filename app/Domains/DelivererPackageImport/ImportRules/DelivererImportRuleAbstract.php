<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Entities\DelivererImportRule;

abstract class DelivererImportRuleAbstract
{
    private $importRuleEntity;

    public function __construct(DelivererImportRule $delivererImportRule)
    {
        $this->importRuleEntity = $delivererImportRule;
    }
}
