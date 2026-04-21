<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Transaction;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class TransactionRepositoryEloquent
 * @package App\Repositories
 */
class TransactionRepositoryEloquent extends BaseRepository implements TransactionRepository
{
    public function model(): string
    {
        return Transaction::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
