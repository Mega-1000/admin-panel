<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Label;
use Illuminate\Support\Collection;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\Order;

class OrderRepositoryEloquent extends BaseRepository implements OrderRepository
{
    public function model(): string
    {
        return Order::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getOrdersForExcelFile(string $from, string $to): Collection
    {
        return $this->with([
            'labels'
        ])->whereHas('labels', function ($query) {
            $query->where('label_id', Label::BOOKED_FIRST_PAYMENT)
                ->orWhere('label_id', Label::ORDER_ITEMS_CONSTRUCTED)
                ->orWhere('label_id', Label::PACKAGE_NOTIFICATION_LABEL);
        })->findWhere([
            ['id', '>=', $from],
            ['id', '<=', $to]
        ]);
    }
}
