<?php

declare(string_types=1);

namespace App\Repositories;

use App\Entities\ProductStockPacket;
use App\Entities\ProductStockPacketItem;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class ProductStockPacketItemRepositoryEloquent extends BaseRepository implements ProductStockPacketItemRepository
{
    public function model(): string
    {
        return ProductStockPacketItem::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
