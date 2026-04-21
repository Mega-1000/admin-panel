<?php

namespace App\Repositories;

use App\Entities\OrderAddress;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Validators\CustomerAddressValidator;

/**
 * Class OrderAddressRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderAddressRepositoryEloquent extends BaseRepository implements OrderAddressRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderAddress::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
