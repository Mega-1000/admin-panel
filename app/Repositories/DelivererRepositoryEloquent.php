<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Deliverer;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class TaskTimeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class DelivererRepositoryEloquent extends BaseRepository implements DelivererRepository
{
    public function model(): string
    {
        return Deliverer::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
