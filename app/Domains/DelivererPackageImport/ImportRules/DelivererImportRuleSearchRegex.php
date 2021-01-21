<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Domains\DelivererPackageImport\Exceptions\TooManyOrdersInDBException;
use App\Entities\Order;

class DelivererImportRuleSearchRegex extends DelivererImportRuleAbstract implements DelivererImportRuleInterface
{
    public function run(): ?Order
    {
        $this->dataToImport = $this->getData();

        if (!$this->validate()) {
            return null;
        }

        $order = $this->columnRepository->findOrder($this->parsedData);

        if ($order->isNotEmpty() && $order->count() > 1) {
            throw new TooManyOrdersInDBException(
                "Znaleziono więcej niż jedno zamówienie w bazie danych dla LP: {$this->parsedData}"
            );
        }

        if ($order->isNotEmpty() && $order->count() === 1) {
            return $order->first();
        }

        return null;
    }

    private function validate(): bool
    {
        if (preg_match("/{$this->getValue()}(.*),/iU", $this->dataToImport, $match)) {
            $this->parsedData = $match[1];

            return true;
        }

        return false;
    }
}
