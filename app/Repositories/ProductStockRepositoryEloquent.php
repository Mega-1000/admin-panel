<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ProductStockRepository;
use App\Entities\ProductStock;
use App\Validators\ProductStockValidator;

/**
 * Class ProductStockRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProductStockRepositoryEloquent extends BaseRepository implements ProductStockRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProductStock::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
