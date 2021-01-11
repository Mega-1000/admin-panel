<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\OrderPackageRealCostForCompany;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class OrderPackageRealCostForCompanyRepositoryEloquent extends BaseRepository implements OrderPackageRepository
{
    public function model(): string
    {
        return OrderPackageRealCostForCompany::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
