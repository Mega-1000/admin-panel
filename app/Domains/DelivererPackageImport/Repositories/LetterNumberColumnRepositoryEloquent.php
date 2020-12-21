<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Entities\Order;
use App\Repositories\OrderPackageRepositoryEloquent;
use Illuminate\Support\Collection;

class LetterNumberColumnRepositoryEloquent extends OrderPackageRepositoryEloquent implements DelivererImportRuleColumnRepositoryInterface
{
    public function findOrder($valueToSearch): ?Collection
    {
        $order = $this->findWhere([
            DelivererRulesColumnNameEnum::ORDER_PACKAGES_LETTER_NUMBER => $valueToSearch,
        ])->first();

        return $order ? $order->order()->get() : null;
    }

    public function updateColumn(Order $order, $valueToUpdate)
    {
        return null;
    }
}
