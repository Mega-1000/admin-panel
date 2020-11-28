<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Entities\Order;
use Illuminate\Support\Collection;

class DelivererImportRuleSearchCompare extends DelivererImportRuleAbstract
{
    public function run(array $line): ?Order
    {
        $this->line = $line;

        /* @var $order Collection */
        $order = $this->findOrder();

        if ($order->count() > 1) {
            throw new \Exception('Too many orders were found for rule');
        }

        return $order->first();
    }

    private function findOrder(): Collection
    {
        return $this->orderRepository->findWhere([
            $this->getDbColumnName()->value => $this->getDataToImport(),
        ]);
    }
}
