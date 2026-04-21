<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use Exception;

class DelivererImportRuleGetWithCondition extends DelivererImportRuleAbstract implements DelivererImportRuleInterface
{
    /**
     * @return mixed
     * @throws Exception
     */
    public function run()
    {
        if ($this->getConditionData() === $this->getConditionValue()) {
            return $this->columnRepository->updateColumn(
                $this->order,
                $this->importRuleEntity,
                $this->getData(),
                $this->valueUsedToFindOrder
            );
        }

        return null;
    }
}
