<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Entities\Order;
use App\Repositories\OrderRepositoryEloquent;

class SelloIdColumnRepositoryEloquent extends OrderRepositoryEloquent implements DelivererImportRuleColumnRepositoryInterface
{
    public function findOrder($valueToSearch)
    {
        return $this->findWhere([
            DelivererRulesColumnNameEnum::ORDER_SELLO_ID => $valueToSearch,
        ]);
    }

    public function updateColumn(Order $order, $valueToUpdate)
    {
        return null;
    }
}
