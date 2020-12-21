<?php

declare(strict_types=1);

namespace App\Domains\DelivererPackageImport\Repositories;

use App\Entities\Order;
use Illuminate\Support\Collection;

interface DelivererImportRuleColumnRepositoryInterface
{
    /**
     * Method should return null if it's not allowed to run for proper repository,
     * otherwise a body can be implemented and method should find a order in proper db table.
     *
     * @param $valueToSearch
     * @return Collection|null
     */
    public function findOrder($valueToSearch): ?Collection;

    /**
     * Method should return null if it's not allowed to run for proper repository,
     * otherwise a body can be implemented and method should update column we want.
     *
     * @param Order $order
     * @param $valueToUpdate
     * @return mixed
     */
    public function updateColumn(Order $order, $valueToUpdate);
}
