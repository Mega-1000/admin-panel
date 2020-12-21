<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Entities\Order;
use App\Repositories\OrderPackageRepositoryEloquent;
use Illuminate\Support\Collection;

class RealCostForCompanyColumnRepositoryEloquent extends OrderPackageRepositoryEloquent implements DelivererImportRuleColumnRepositoryInterface
{
    public function findOrder($valueToSearch): ?Collection
    {
        return null;
    }

    public function updateColumn(Order $order, $valueToUpdate)
    {
        $orderPackage = $this->findWhere([
            'order_id' => $order->id,
        ])->first();

        if ($orderPackage) {
            $orderPackage->{DelivererRulesColumnNameEnum::ORDER_PACKAGES_REAL_COST_FOR_COMPANY} = $valueToUpdate;

            return $orderPackage->save();
        }

        return null;
    }
}
