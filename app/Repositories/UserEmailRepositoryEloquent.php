<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserEmailRepository;
use App\Entities\UserEmail;
use Illuminate\Support\Facades\DB;

/**
 * Class UserEmailRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserEmailRepositoryEloquent extends BaseRepository implements UserEmailRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserEmail::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
