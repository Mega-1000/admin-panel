<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Entities\Order;

interface DelivererImportRuleColumnRepositoryInterface
{
    public function findOrder($valueToSearch);//todo ?Order;
    public function updateColumn(Order $order, $valueToUpdate);// todo return type
}
