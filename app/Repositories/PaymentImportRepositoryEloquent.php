<?php

namespace App\Repositories;

use App\Entities\PaymentImport;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;


/**
 * Class ProductStockRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PaymentImportRepositoryEloquent extends BaseRepository implements PaymentImportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PaymentImport::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
