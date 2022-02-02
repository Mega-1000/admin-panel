<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\ProviderTransaction;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Provider Transaction class
 */
class ProviderTransactionRepositoryEloquent extends BaseRepository implements ProviderTransactionRepository
{
    public function model(): string
    {
        return ProviderTransaction::class;
    }

    public function boot(): void
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getBalance(string $courierSymbol): float
    {
        $lastTransaction = $this->findByField('provider', $courierSymbol)->last();
        if (empty($lastTransaction)) {
            return 0;
        } else {
            return $lastTransaction->provider_balance;
        }
    }

    public function getBalanceOnInvoice(string $courierSymbol, $invoiceNumber): float
    {
        $lastTransaction = $this->findWhere(
            [
                'provider' => $courierSymbol,
                'invoice_number' => $invoiceNumber
            ]
        )->last();
        if (empty($lastTransaction)) {
            return 0;
        } else {
            return $lastTransaction->provider_balance_on_invoice;
        }
    }
}
