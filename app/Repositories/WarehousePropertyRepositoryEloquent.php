<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\WarehousePropertyRepository;
use App\Entities\WarehouseProperty;
use App\Validators\WarehousePropertyValidator;

/**
 * Class WarehousePropertyRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WarehousePropertyRepositoryEloquent extends BaseRepository implements WarehousePropertyRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WarehouseProperty::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
