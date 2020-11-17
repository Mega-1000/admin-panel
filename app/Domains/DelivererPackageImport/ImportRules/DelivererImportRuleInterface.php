<?php

namespace App\Domains\DelivererPackageImport\ImportRules;

use App\Entities\DelivererImportRule;

interface DelivererImportRuleInterface
{
    function getImportRuleEntity(): DelivererImportRule;
    function run(array $line);
}
