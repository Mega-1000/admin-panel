<?php

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Entities\DelivererImportRule;
use App\Entities\Order;

interface DelivererImportRuleInterface
{
    function getImportRuleEntity(): DelivererImportRule;
    function run(array $line);
    function setOrder(Order $order): void;
}
