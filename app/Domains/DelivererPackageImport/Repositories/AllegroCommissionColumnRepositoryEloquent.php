<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Domains\DelivererPackageImport\PriceFormatter;
use App\Entities\Order;
use App\Repositories\OrderAllegroCommissionRepositoryEloquent;
use Illuminate\Support\Collection;

class AllegroCommissionColumnRepositoryEloquent extends OrderAllegroCommissionRepositoryEloquent implements DelivererImportRuleColumnRepositoryInterface
{
    public function findOrder($valueToSearch): ?Collection
    {
        return null;
    }

    public function updateColumn(Order $order, $valueToUpdate)
    {
        return $this->create([
            'order_id' => $order->id,
            'amount' => PriceFormatter::asAbsolute(PriceFormatter::fromString($valueToUpdate)),
        ]);
    }
}
