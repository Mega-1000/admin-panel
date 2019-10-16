<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\SpeditionExchangeOfferRepository;
use App\Entities\SpeditionExchangeOffer;
use App\Validators\SpeditionExchangeOfferValidator;

/**
 * Class SpeditionExchangeOfferRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SpeditionExchangeOfferRepositoryEloquent extends BaseRepository implements SpeditionExchangeOfferRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SpeditionExchangeOffer::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
