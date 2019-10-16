<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderPackageRepository;
use App\Entities\OrderPackage;
use App\Validators\OrderPackageValidator;

/**
 * Class OrderPackageRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderPackageRepositoryEloquent extends BaseRepository implements OrderPackageRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderPackage::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
