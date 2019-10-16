<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ProductPriceRepository;
use App\Entities\ProductPrice;
use App\Validators\ProductPriceValidator;

/**
 * Class ProductPriceRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProductPriceRepositoryEloquent extends BaseRepository implements ProductPriceRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProductPrice::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
