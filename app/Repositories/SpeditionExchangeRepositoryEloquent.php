<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\SpeditionExchangeRepository;
use App\Entities\SpeditionExchange;
use App\Validators\SpeditionExchangeValidator;

/**
 * Class SpeditionExchangeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SpeditionExchangeRepositoryEloquent extends BaseRepository implements SpeditionExchangeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SpeditionExchange::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
