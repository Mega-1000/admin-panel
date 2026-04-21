<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderTaskRepository;
use App\Entities\OrderTask;
use App\Validators\OrderTaskValidator;

/**
 * Class OrderTaskRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderTaskRepositoryEloquent extends BaseRepository implements OrderTaskRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderTask::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
