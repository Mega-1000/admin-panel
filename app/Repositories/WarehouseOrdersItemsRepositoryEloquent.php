<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\WarehouseOrdersItemsRepository;
use App\Entities\WarehouseOrderItem;


/**
 * Class WarehouseOrdersItemsRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WarehouseOrdersItemsRepositoryEloquent extends BaseRepository implements WarehouseOrdersItemsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WarehouseOrderItem::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
