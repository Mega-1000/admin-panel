<?php

namespace App\Repositories;

use App\Entities\OrderAllegro;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class OrderAllegroRepositoryEloquent
 * @package App\Repositories
 *
 * @author Norbert Grzechnik <norbert.grzechnik@netro42.digital>
 */
class OrderAllegroRepositoryEloquent extends BaseRepository implements OrderAllegroRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderAllegro::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}