<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\TaskSalaryDetailsRepository;
use App\Entities\TaskSalaryDetails;
use App\Validators\TaskSalaryDetailsValidator;

/**
 * Class TaskSalaryDetailsRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class TaskSalaryDetailsRepositoryEloquent extends BaseRepository implements TaskSalaryDetailsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return TaskSalaryDetails::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
