<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Entities\Order;

class DelivererImportRuleSearchRegex extends DelivererImportRuleAbstract
{
    public function run(array $line): ?Order
    {
        $this->line = $line;

        $data = $this->getDataToImport();
        if (strpos($data, $this->getValue()) === 0) {
            $parsedData = substr($data, strlen($this->getValue()));

            $order = $this->orderRepository->findWhere([
                $this->getDbColumnName()->value => $parsedData
            ]);

            if ($order->count() > 1) {
                throw new \Exception('Too many orders were found for rule');
            }

            return $order->first();
        }

        return null;
    }
}
