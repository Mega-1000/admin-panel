<?php

declare(strict_types=1);

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\OrderPackage;

class OrderPackageRepositoryEloquent extends BaseRepository implements OrderPackageRepository
{
    public function model(): string
    {
        return OrderPackage::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
