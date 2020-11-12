<?php

namespace App\Domains\DelivererPackageImport\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface DelivererImportRuleRepositoryInterface extends RepositoryInterface
{
    function saveImportRules(array $delivererImportRules);
}
