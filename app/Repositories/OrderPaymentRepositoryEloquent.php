<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderPaymentRepository;
use App\Entities\OrderPayment;
use App\Validators\OrderPaymentValidator;

/**
 * Class OrderPaymentRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderPaymentRepositoryEloquent extends BaseRepository implements OrderPaymentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderPayment::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
