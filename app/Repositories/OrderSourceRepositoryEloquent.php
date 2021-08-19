<?php

namespace App\Repositories;

use App\Entities\OrderSource;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class OrderSourceRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderSourceRepositoryEloquent extends BaseRepository implements OrderSourceRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderSource::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
