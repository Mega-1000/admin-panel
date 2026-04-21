<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderWarehouseNotificationRepository;
use App\Entities\OrderWarehouseNotification;
use App\Validators\OrderShipmentNotificationValidator;

/**
 * Class OrderShipmentNotificationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderWarehouseNotificationRepositoryEloquent extends BaseRepository implements OrderWarehouseNotificationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderWarehouseNotification::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
