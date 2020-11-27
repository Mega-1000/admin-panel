<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Entities\Order;
use Illuminate\Support\Collection;

class DelivererImportRuleSearchRegex extends DelivererImportRuleAbstract
{
    public function run(array $line): ?Order
    {
        $this->line = $line;
        $this->dataToImport = $this->getDataToImport();

        if (!$this->validate()) {
            return null;
        }

        /* @var $order Collection */
        $order = $this->findOrder();

        if ($order->count() > 1) {
            throw new \Exception('Too many orders were found for rule');
        }

        return $order->first();
    }

    private function validate(): bool
    {
        return strpos($this->dataToImport, $this->getValue()) === 0;
    }

    private function parseData(): string
    {
        return substr($this->dataToImport, strlen($this->getValue()));
    }

    private function findOrder(): ?Order
    {
        return $this->orderRepository->findWhere([
            $this->getDbColumnName()->value => $this->parseData(),
        ]);
    }
}
