<?php

namespace App\Repositories;

use App\Entities\OrderPaymentLog;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class CategoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderPaymentLogRepositoryEloquent extends BaseRepository implements OrderPaymentLogRepository
{
    public function model()
    {
        return OrderPaymentLog::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
