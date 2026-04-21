<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\TaskTimeRepository;
use App\Entities\TaskTime;
use App\Validators\TaskTimeValidator;

/**
 * Class TaskTimeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class TaskTimeRepositoryEloquent extends BaseRepository implements TaskTimeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return TaskTime::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
