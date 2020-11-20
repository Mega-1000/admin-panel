<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Entities\Order;

class DelivererImportRuleGetAndReplace extends DelivererImportRuleAbstract
{
    public function run(array $line): ?Order
    {
        $this->line = $line;

        if ($this->getValue() === $this->getDataToImport()) {
            return $this->orderRepository->update([
                $this->getDbColumnName()->value => $this->getChangeTo(),
            ], $this->order->id);
        }

        return null;
    }
}
