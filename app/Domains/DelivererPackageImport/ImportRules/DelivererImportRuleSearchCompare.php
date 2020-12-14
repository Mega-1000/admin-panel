<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Entities\Order;

class DelivererImportRuleSearchCompare extends DelivererImportRuleAbstract
{
    public function run(): ?Order
    {
        $order = $this->columnRepository->findOrder($this->getData());

        if ($order->count() > 1) {
            throw new \Exception('Too many orders were found for rule');
        }

        return $order->first();
    }
}
