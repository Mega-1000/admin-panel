<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CustomerAddressRepository;
use App\Entities\CustomerAddress;
use App\Validators\CustomerAddressValidator;

/**
 * Class CustomerAddressRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CustomerAddressRepositoryEloquent extends BaseRepository implements CustomerAddressRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CustomerAddress::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
