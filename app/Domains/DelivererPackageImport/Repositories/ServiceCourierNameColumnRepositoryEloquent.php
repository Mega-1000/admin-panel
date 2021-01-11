<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Entities\DelivererImportRule;
use App\Entities\Order;
use App\Repositories\OrderPackageRepositoryEloquent;
use Illuminate\Support\Collection;

class ServiceCourierNameColumnRepositoryEloquent implements DelivererImportRuleColumnRepositoryInterface
{
    private $orderPackageRepositoryEloquent;

    public function __construct(OrderPackageRepositoryEloquent $orderPackageRepositoryEloquent)
    {
        $this->orderPackageRepositoryEloquent = $orderPackageRepositoryEloquent;
    }

    public function findOrder($valueToSearch): ?Collection
    {
        return null;
    }

    public function updateColumn(Order $order, DelivererImportRule $delivererImportRule, $valueToUpdate)
    {
        $orderPackage = $this->orderPackageRepositoryEloquent->findWhere([
            'order_id' => $order->id,
        ])->first();

        if ($orderPackage) {
            $orderPackage->{DelivererRulesColumnNameEnum::ORDER_PACKAGES_SERVICE_COURIER_NAME} = $valueToUpdate;

            return $orderPackage->save();
        }

        return null;
    }
}
