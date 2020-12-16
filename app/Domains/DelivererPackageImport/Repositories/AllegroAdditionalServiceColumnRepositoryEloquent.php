<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
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
        return $this->update([
            DelivererRulesColumnNameEnum::ORDER_ALLEGRO_ADDITIONAL_SERVICE => $valueToUpdate,
        ], $order->id);
    }
}
