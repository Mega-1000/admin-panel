<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Entities\Order;
use App\Repositories\OrderRepositoryEloquent;
use Illuminate\Support\Collection;

class AllegroOperationDateColumnRepositoryEloquent extends OrderRepositoryEloquent implements DelivererImportRuleColumnRepositoryInterface
{
    public function findOrder($valueToSearch): ?Collection
    {
        return null;
    }

    public function updateColumn(Order $order, $valueToUpdate)
    {
        return $this->update([
            DelivererRulesColumnNameEnum::ORDER_ALLEGRO_OPERATION_DATE => $valueToUpdate,
        ], $order->id);
    }
}
