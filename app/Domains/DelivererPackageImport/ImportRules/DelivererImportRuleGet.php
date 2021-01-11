<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use Exception;

class DelivererImportRuleGet extends DelivererImportRuleAbstract implements DelivererImportRuleInterface
{
    /**
     * @return mixed
     * @throws Exception
     */
    public function run()
    {
        return $this->columnRepository->updateColumn(
            $this->order,
            $this->importRuleEntity,
            $this->getData()
        );
    }
}
