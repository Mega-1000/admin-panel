<?php

namespace App\Repositories;

use App\Enums\OrderPaymentPromiseType;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\OrderPayment;

/**
 * Class OrderPaymentRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderPaymentRepositoryEloquent extends BaseRepository implements OrderPaymentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderPayment::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getPromisedPayment(int $orderId, string $amount)
    {
        return parent::findWhere([
            'order_id' => $orderId,
            'amount' => $amount,
            'promise' => OrderPaymentPromiseType::PROMISED
        ])->first();
    }

}
