<?php

namespace App\Repositories;

use App\Entities\Deliverer;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface TaskTimeRepository.
 *
 * @package namespace App\Repositories;
 */
interface DelivererRepository extends RepositoryInterface
{
    function findById(int $delivererId): ?Deliverer;
    function removeDeliverer(Deliverer $deliverer): bool;
}
