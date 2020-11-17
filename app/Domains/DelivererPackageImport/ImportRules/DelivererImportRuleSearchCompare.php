<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Entities\Order;

class DelivererImportRuleSearchCompare extends DelivererImportRuleAbstract
{
    public function run(array $line): ?Order
    {
        $order = $this->orderRepository->findWhere([
            $this->getDbColumnName()->value => $this->getImportColumnNumber()->get()
        ]);

        if ($order->count() > 1) {
            throw new \Exception('Too many orders were found for rule');
        }

        if ($order->count() === 1) {
            return $order->first();
        }

        return null;
    }
}
