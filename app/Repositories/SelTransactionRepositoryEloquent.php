<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\SelTransaction;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class SelTransactionRepositoryEloquent extends BaseRepository implements CategoryRepository
{
    public function model(): string
    {
        return SelTransaction::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
