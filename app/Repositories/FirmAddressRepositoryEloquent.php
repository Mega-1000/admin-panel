<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\FirmAddressRepository;
use App\Entities\FirmAddress;
use App\Validators\FirmAddressValidator;

/**
 * Class FirmAddressRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FirmAddressRepositoryEloquent extends BaseRepository implements FirmAddressRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return FirmAddress::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
