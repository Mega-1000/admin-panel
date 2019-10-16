<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\WarehouseRepository;
use App\Entities\Warehouse;
use App\Validators\WarehouseValidator;

/**
 * Class WarehouseRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WarehouseRepositoryEloquent extends BaseRepository implements WarehouseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Warehouse::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
