<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Entities\ProductStockPacket;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;

class ProductStockPacketRepositoryEloquent extends BaseRepository implements ProductStockPacketRepository
{
    public function model(): string
    {
        return ProductStockPacket::class;
    }

    /**
     * @throws RepositoryException
     */
    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
