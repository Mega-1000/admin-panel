<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Entities\DelivererImportRule;
use App\Entities\Order;
use App\Repositories\OrderRepositoryEloquent;
use Illuminate\Support\Collection;

class PaymentChannelColumnRepositoryEloquent implements DelivererImportRuleColumnRepositoryInterface
{
    private $orderRepositoryEloquent;

    public function __construct(OrderRepositoryEloquent $orderRepositoryEloquent)
    {
        $this->orderRepositoryEloquent = $orderRepositoryEloquent;
    }

    public function findOrder($valueToSearch): ?Collection
    {
        return null;
    }

    public function updateColumn(Order $order, DelivererImportRule $delivererImportRule, $valueToUpdate)
    {
        return $this->orderRepositoryEloquent->update([
            DelivererRulesColumnNameEnum::ORDER_PAYMENT_CHANNEL => $valueToUpdate,
        ], $order->id);
    }
}
