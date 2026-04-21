<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ReportPropertyRepository;
use App\Entities\ReportProperty;
use App\Validators\ReportPropertyValidator;

/**
 * Class ReportPropertyRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ReportPropertyRepositoryEloquent extends BaseRepository implements ReportPropertyRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ReportProperty::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
