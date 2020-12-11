<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Entities\Order;
use Illuminate\Support\Collection;

class DelivererImportRuleSearchRegex extends DelivererImportRuleAbstract
{
    private $parsedData;

    public function run(array $line): ?Order
    {
        $this->line = $line;
        $this->dataToImport = $this->getDataToImport();

        if (!$this->validate()) {
            return null;
        }

        $order = $this->findOrder();

        if ($order->count() > 1) {
            throw new \Exception('Too many orders were found for rule');
        }

        return $order->first();
    }

    private function validate(): bool
    {
        if (preg_match("/{$this->getValue()}(.*),/iU", $this->dataToImport, $match)) {
            $this->parsedData = $match[1];

            return true;
        }

        return false;
    }

    private function findOrder(): Collection
    {
        return $this->orderRepository->findWhere([
            $this->getDbColumnName()->value => $this->parsedData,
        ]);
    }
}
