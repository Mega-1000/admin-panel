<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ReportDailyRepository;
use App\Entities\ReportDaily;
use App\Validators\ReportDailyValidator;

/**
 * Class ReportDailyRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ReportDailyRepositoryEloquent extends BaseRepository implements ReportDailyRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ReportDaily::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
