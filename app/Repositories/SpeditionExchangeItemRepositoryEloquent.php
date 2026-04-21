<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\SpeditionExchangeItemRepository;
use App\Entities\SpeditionExchangeItem;
use App\Validators\SpeditionExchangeItemValidator;

/**
 * Class SpeditionExchangeItemRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SpeditionExchangeItemRepositoryEloquent extends BaseRepository implements SpeditionExchangeItemRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SpeditionExchangeItem::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
