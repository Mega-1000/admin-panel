<?php

namespace App\Repositories;

use App\Entities\ProductStockPacket;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class CategoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProductStockPacketRepositoryEloquent extends BaseRepository implements ProductStockPacketRepository
{
    public function model(): string
    {
        return ProductStockPacket::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
