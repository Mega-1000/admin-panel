<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Entities\Order;

class DelivererImportRuleSearchRegex extends DelivererImportRuleAbstract implements DelivererImportRuleInterface
{
    private $parsedData;

    public function run(): Order
    {
        $this->dataToImport = $this->getData();

        if (!$this->validate()) {
            throw new \Exception('Data could not be parsed');
        }

        $order = $this->columnRepository->findOrder($this->parsedData);

        if (is_null($order)) {
            throw new \Exception('Order for ' . $this->parsedData . ' was not found');
        }

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
}
