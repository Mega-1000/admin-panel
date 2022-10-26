<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\ShipmentGroup;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class ShipmentGroupRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ShipmentGroupRepositoryEloquent extends BaseRepository implements ShipmentGroupRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return ShipmentGroup::class;
    }


    /**
     * Boot up the repository, pushing criteria
     *
     * @throws RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
