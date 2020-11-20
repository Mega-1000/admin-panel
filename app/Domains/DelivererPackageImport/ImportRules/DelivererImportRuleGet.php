<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Entities\Order;

class DelivererImportRuleGet extends DelivererImportRuleAbstract
{
    public function run(array $line): Order
    {
        $this->line = $line;

        return $this->orderRepository->update([
            $this->getDbColumnName()->value => $this->getDataToImport(),
        ], $this->order->id);
    }
}
