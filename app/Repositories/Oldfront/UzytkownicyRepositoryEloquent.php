<?php

namespace App\Repositories\Oldfront;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Oldfront\UzytkownicyRepository;
use App\Entities\Oldfront\Uzytkownicy;
use App\Validators\Oldfront\UzytkownicyValidator;

/**
 * Class UzytkownicyRepositoryEloquent.
 *
 * @package namespace App\Repositories\Oldfront;
 */
class UzytkownicyRepositoryEloquent extends BaseRepository implements UzytkownicyRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Uzytkownicy::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
