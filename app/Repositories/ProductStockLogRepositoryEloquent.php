<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ProductStockLogRepository;
use App\Entities\ProductStockLog;
use App\Validators\ProductStockLogValidator;

/**
 * Class ProductStockLogRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProductStockLogRepositoryEloquent extends BaseRepository implements ProductStockLogRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProductStockLog::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
