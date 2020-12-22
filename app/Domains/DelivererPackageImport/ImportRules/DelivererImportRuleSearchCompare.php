<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Entities\Order;

class DelivererImportRuleSearchCompare extends DelivererImportRuleAbstract
{
    public function run(): Order
    {
        $order = $this->columnRepository->findOrder($this->getData());

        if (is_null($order)) {
            throw new \Exception('Order for ' . $this->getData() . ' was not found');
        }

        if ($order->count() > 1) {
            throw new \Exception('Too many orders were found for rule');
        }

        return $order->first();
    }
}
