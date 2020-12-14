<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Entities\Order;
use App\Repositories\OrderRepositoryEloquent;

class AllegroAdditionalServiceColumnRepositoryEloquent extends OrderRepositoryEloquent implements DelivererImportRuleColumnRepositoryInterface
{
    public function findOrder($valueToSearch)
    {
        return null;
    }

    public function updateColumn(Order $order, $valueToUpdate)
    {
        return null;
    }
}
