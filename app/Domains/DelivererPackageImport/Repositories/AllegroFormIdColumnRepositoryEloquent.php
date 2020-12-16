<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Entities\Order;
use App\Repositories\OrderRepositoryEloquent;

class AllegroFormIdColumnRepositoryEloquent extends OrderRepositoryEloquent implements DelivererImportRuleColumnRepositoryInterface
{
    public function findOrder($valueToSearch)
    {
        return $this->findWhere([
            DelivererRulesColumnNameEnum::ORDER_ALLEGRO_FORM_ID => $valueToSearch,
        ]);
    }

    public function updateColumn(Order $order, $valueToUpdate)
    {
        return null;
    }
}
