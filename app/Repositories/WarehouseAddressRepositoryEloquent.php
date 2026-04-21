<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\WarehouseAddressRepository;
use App\Entities\WarehouseAddress;
use App\Validators\WarehouseAddressValidator;

/**
 * Class WarehouseAddressRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WarehouseAddressRepositoryEloquent extends BaseRepository implements WarehouseAddressRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WarehouseAddress::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
