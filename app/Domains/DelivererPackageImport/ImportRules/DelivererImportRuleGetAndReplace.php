<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Entities\Order;

class DelivererImportRuleGetAndReplace extends DelivererImportRuleAbstract
{
    public function run(): ?Order
    {
        if (!$this->validate()) {
            return null;
        }

        return $this->columnRepository->updateColumn($this->order, $this->getChangeTo());
    }

    private function validate(): bool
    {
        return $this->getValue() === $this->getData();
    }
}
