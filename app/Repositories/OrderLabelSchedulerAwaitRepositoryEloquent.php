<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderLabelSchedulerAwaitRepository;
use App\Entities\OrderLabelSchedulerAwait;
use App\Validators\OrderLabelSchedulerAwaitValidator;

/**
 * Class OrderLabelSchedulerAwaitRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderLabelSchedulerAwaitRepositoryEloquent extends BaseRepository implements OrderLabelSchedulerAwaitRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderLabelSchedulerAwait::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
