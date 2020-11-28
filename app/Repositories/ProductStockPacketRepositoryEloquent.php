<?php 

declare(string_types=1);

namespace App\Repositories;

use App\Entities\ProductStockPacket;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

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
