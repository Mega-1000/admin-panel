<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Entities\Order;
use Illuminate\Support\Collection;

interface DelivererImportRuleColumnRepositoryInterface
{
    public function findOrder($valueToSearch): ?Collection;
    public function updateColumn(Order $order, $valueToUpdate);
}
