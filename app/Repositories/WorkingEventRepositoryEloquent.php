<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Transaction;
use App\Entities\WorkingEvents;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 *
 */
class WorkingEventRepositoryEloquent extends BaseRepository implements WorkingEventRepository
{
    public function model(): string
    {
        return WorkingEvents::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
