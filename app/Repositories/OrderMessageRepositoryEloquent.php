<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderMessageRepository;
use App\Entities\OrderMessage;
use App\Validators\OrderMessageValidator;

/**
 * Class OrderMessageRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderMessageRepositoryEloquent extends BaseRepository implements OrderMessageRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderMessage::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
