<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Domains\DelivererPackageImport\Exceptions\TooManyOrdersInDBException;
use App\Entities\Order;

class DelivererImportRuleSearchCompare extends DelivererImportRuleAbstract implements DelivererImportRuleInterface
{
    public function run(): ?Order
    {
        if (empty($this->getData())) {
            return null;
        }

        $order = $this->columnRepository->findOrder($this->getData());

        if (empty($order)) {
            return null;
        }

        if ($order->isNotEmpty() && $order->count() > 1) {
            throw new TooManyOrdersInDBException(
                "Znaleziono więcej niż jedno zamówienie w bazie danych dla LP: {$this->getData()}"
            );
        }

        if ($order->isNotEmpty() && $order->count() === 1) {
            return $order->first();
        }

        return null;
    }
}
