<?php

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Entities\Deliverer;
use Prettus\Repository\Contracts\RepositoryInterface;

interface DelivererImportRuleRepositoryInterface extends RepositoryInterface
{
    function saveImportRules(array $delivererImportRules);
    function getDelivererImportRules(Deliverer $deliverer);
    function removeDelivererImportRules(Deliverer $deliverer): bool;
}
