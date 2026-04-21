<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\FirmRepository;
use App\Entities\Firm;
use App\Validators\FirmValidator;

/**
 * Class FirmRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FirmRepositoryEloquent extends BaseRepository implements FirmRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Firm::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
