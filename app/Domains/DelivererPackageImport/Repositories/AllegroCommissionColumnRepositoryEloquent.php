<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Domains\DelivererPackageImport\PriceFormatter;
use App\Entities\DelivererImportRule;
use App\Entities\Order;
use App\Repositories\OrderAllegroCommissionRepositoryEloquent;
use Illuminate\Support\Collection;

class AllegroCommissionColumnRepositoryEloquent implements DelivererImportRuleColumnRepositoryInterface
{
    private $orderAllegroCommissionRepositoryEloquent;

    public function __construct(OrderAllegroCommissionRepositoryEloquent $orderAllegroCommissionRepositoryEloquent)
    {
        $this->orderAllegroCommissionRepositoryEloquent = $orderAllegroCommissionRepositoryEloquent;
    }

    public function findOrder($valueToSearch): ?Collection
    {
        return null;
    }

    public function updateColumn(Order $order, DelivererImportRule $delivererImportRule, $valueToUpdate)
    {
        return $this->orderAllegroCommissionRepositoryEloquent->create([
            'order_id' => $order->id,
            'amount' => PriceFormatter::asAbsolute(PriceFormatter::fromString($valueToUpdate)),
        ]);
    }
}
