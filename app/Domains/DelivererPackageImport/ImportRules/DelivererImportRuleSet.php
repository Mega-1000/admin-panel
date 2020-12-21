<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

class DelivererImportRuleSet extends DelivererImportRuleAbstract
{
    /**
     * @return mixed
     */
    public function run()
    {
        return $this->columnRepository->updateColumn($this->order, $this->getValue());
    }
}
