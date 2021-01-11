<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Entities\DelivererImportRule;
use App\Entities\Order;
use App\Repositories\OrderPackageRepositoryEloquent;
use Illuminate\Support\Collection;

class LetterNumberColumnRepositoryEloquent implements DelivererImportRuleColumnRepositoryInterface
{
    private $orderPackageRepositoryEloquent;

    public function __construct(OrderPackageRepositoryEloquent $orderPackageRepositoryEloquent)
    {
        $this->orderPackageRepositoryEloquent = $orderPackageRepositoryEloquent;
    }

    public function findOrder($valueToSearch): ?Collection
    {
        $order = $this->orderPackageRepositoryEloquent->findWhere([
            DelivererRulesColumnNameEnum::ORDER_PACKAGES_LETTER_NUMBER => $valueToSearch,
        ])->first();

        return $order ? $order->order()->get() : null;
    }

    public function updateColumn(Order $order, DelivererImportRule $delivererImportRule, $valueToUpdate)
    {
        return null;
    }
}
