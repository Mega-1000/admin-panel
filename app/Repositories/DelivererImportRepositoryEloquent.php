<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\DelivererImport;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class DelivererImportRepositoryEloquent extends BaseRepository implements OrderPackageRepository
{
    public function model(): string
    {
        return DelivererImport::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
