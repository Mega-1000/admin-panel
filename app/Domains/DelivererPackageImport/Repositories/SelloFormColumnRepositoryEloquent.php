<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Entities\DelivererImportRule;
use App\Entities\Order;
use App\Repositories\SelTransactionRepositoryEloquent;
use Illuminate\Support\Collection;

class SelloFormColumnRepositoryEloquent implements DelivererImportRuleColumnRepositoryInterface
{
    private $selTransactionRepositoryEloquent;

    public function __construct(SelTransactionRepositoryEloquent $selTransactionRepositoryEloquent)
    {
        $this->selTransactionRepositoryEloquent = $selTransactionRepositoryEloquent;
    }

    public function findOrder($valueToSearch): ?Collection
    {
        $order = $this->selTransactionRepositoryEloquent->findWhere([
            DelivererRulesColumnNameEnum::SEL_TR_TRANSACTION_SELLO_FORM => $valueToSearch,
        ])->first();

        return $order ? $order->order()->get() : null;
    }

    public function updateColumn(
        Order $order,
        DelivererImportRule $delivererImportRule,
        $valueToUpdate,
        $valueUsedToFindOrder
    ) {
        return null;
    }
}
