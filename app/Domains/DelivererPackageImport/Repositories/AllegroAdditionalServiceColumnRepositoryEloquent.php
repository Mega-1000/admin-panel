<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Entities\DelivererImportRule;
use App\Entities\Order;
use App\Repositories\OrderRepositoryEloquent;
use Illuminate\Support\Collection;

readonly class AllegroAdditionalServiceColumnRepositoryEloquent implements DelivererImportRuleColumnRepositoryInterface
{
    public function __construct(
        private OrderRepositoryEloquent $orderRepositoryEloquent
    ) {}

    public function findOrder($valueToSearch): ?Collection
    {
        return null;
    }

    public function updateColumn(
        Order $order,
        DelivererImportRule $delivererImportRule,
        $valueToUpdate,
        $valueUsedToFindOrder
    ) {
        return $this->orderRepositoryEloquent->update([
            DelivererRulesColumnNameEnum::ORDER_ALLEGRO_ADDITIONAL_SERVICE => $valueToUpdate,
        ], $order->id);
    }
}
