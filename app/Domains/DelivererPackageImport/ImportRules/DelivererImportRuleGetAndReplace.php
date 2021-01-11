<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

class DelivererImportRuleGetAndReplace extends DelivererImportRuleAbstract implements DelivererImportRuleInterface
{
    /**
     * @return mixed
     */
    public function run()
    {
        if (!$this->validate()) {
            return null;
        }

        return $this->columnRepository->updateColumn(
            $this->order,
            $this->importRuleEntity,
            $this->getChangeTo()
        );
    }

    private function validate(): bool
    {
        return $this->getValue() === $this->getData();
    }
}
