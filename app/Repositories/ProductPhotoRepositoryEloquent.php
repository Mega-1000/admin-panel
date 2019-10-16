<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ProductPhotoRepository;
use App\Entities\ProductPhoto;
use App\Validators\ProductPhotoValidator;

/**
 * Class ProductPhotoRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProductPhotoRepositoryEloquent extends BaseRepository implements ProductPhotoRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProductPhoto::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
