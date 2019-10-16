<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\LabelRepository;
use App\Entities\Label;
use App\Validators\LabelsValidator;

/**
 * Class LabelRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class LabelRepositoryEloquent extends BaseRepository implements LabelRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Label::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
