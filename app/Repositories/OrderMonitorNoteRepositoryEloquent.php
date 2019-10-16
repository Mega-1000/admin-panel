<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderMonitorNoteRepository;
use App\Entities\OrderMonitorNote;
use App\Validators\OrderMonitorNoteValidator;

/**
 * Class OrderMonitorNoteRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderMonitorNoteRepositoryEloquent extends BaseRepository implements OrderMonitorNoteRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderMonitorNote::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
