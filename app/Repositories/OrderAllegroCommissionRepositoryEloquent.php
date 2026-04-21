<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\OrderAllegroCommission;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class OrderAllegroCommissionRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderAllegroCommissionRepositoryEloquent extends BaseRepository implements CategoryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return OrderAllegroCommission::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
