<?php

namespace App\Repositories;

use App\Entities\PackageTemplate;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class PackageTemplateRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PackageTemplateRepositoryEloquent extends BaseRepository implements PackageTemplateRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PackageTemplate::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
