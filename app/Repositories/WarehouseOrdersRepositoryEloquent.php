<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\WarehouseOrdersRepository;
use App\Entities\WarehouseOrder;


/**
 * Class WarehouseOrdersRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WarehouseOrdersRepositoryEloquent extends BaseRepository implements WarehouseOrdersRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WarehouseOrder::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
