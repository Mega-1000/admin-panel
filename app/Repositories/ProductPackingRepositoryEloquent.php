<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ProductPackingRepository;
use App\Entities\ProductPacking;
use App\Validators\ProductPackingValidator;

/**
 * Class ProductPackingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProductPackingRepositoryEloquent extends BaseRepository implements ProductPackingRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProductPacking::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
