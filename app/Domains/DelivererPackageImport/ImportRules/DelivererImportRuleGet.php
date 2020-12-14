<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Entities\Order;

class DelivererImportRuleGet extends DelivererImportRuleAbstract
{
    public function run(): Order
    {
        return $this->columnRepository->updateColumn($this->order, $this->getData());
    }
}
