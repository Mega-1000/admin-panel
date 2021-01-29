<?php

namespace App\Domains\DelivererPackageImport\ImportRules;

interface DelivererImportRuleInterface
{
    /**
     * @return mixed
     */
    function run();
}
