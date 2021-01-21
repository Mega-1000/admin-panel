<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Domains\DelivererPackageImport\Exceptions\OrderPackageWasNotFoundException;
use App\Domains\DelivererPackageImport\PriceFormatter;
use App\Entities\DelivererImportRule;
use App\Entities\Order;
use App\Entities\OrderPackage;
use App\Repositories\OrderPackageRepositoryEloquent;
use Illuminate\Support\Collection;

class RealCostForCompanyColumnRepositoryEloquent implements DelivererImportRuleColumnRepositoryInterface
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

    public function updateColumn(Order $order, DelivererImportRule $delivererImportRule, $valueToUpdate): OrderPackage
    {
        /* @var $orderPackage OrderPackage */
        $orderPackage = $this->orderPackageRepositoryEloquent->findWhere([
            'order_id' => $order->id,
        ])->first();

        if ($orderPackage) {
            $orderPackage->realCostsForCompany()->create([
                'order_package_id' => $orderPackage->id,
                'deliverer_id' => $delivererImportRule->deliverer->id,
                'cost' => PriceFormatter::asAbsolute(PriceFormatter::fromString($valueToUpdate))
            ]);

            return $orderPackage;
        }

        throw new OrderPackageWasNotFoundException((string) $order->id);
    }
}
