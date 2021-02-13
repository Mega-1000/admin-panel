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
    public function model(): string
    {
        return OrderPayment::class;
    }

    public function boot(): RequestCriteria
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @return mixed
     */
    public function getPromisedPayment(int $orderId, string $amount): ?OrderPayment
    {
        return $this->findWhere([
            'order_id' => $orderId,
            'amount' => $amount,
            'promise' => OrderPaymentPromiseType::PROMISED,
        ])->first();
    }

}
