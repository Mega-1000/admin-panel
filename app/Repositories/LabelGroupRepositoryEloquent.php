<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\LabelGroupRepository;
use App\Entities\LabelGroup;
use App\Validators\LabelGroupsValidator;

/**
 * Class LabelGroupsRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class LabelGroupRepositoryEloquent extends BaseRepository implements LabelGroupRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return LabelGroup::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
