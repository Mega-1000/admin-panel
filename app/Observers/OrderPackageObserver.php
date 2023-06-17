<?php

namespace App\Observers;

use App\Entities\OrderPackage;
use App\Services\FindOrCreatePaymentForPackageService;
use Illuminate\Support\Str;

final readonly class OrderPackageObserver
{
    public function __construct(
      protected FindOrCreatePaymentForPackageService $findOrCreatePaymentForPackageService
    ) {}

    /**
     * Handle the OrderPackage "created" event.
     *
     * @param OrderPackage $orderPackage
     * @return void
     */
    public function created(OrderPackage $orderPackage): void
    {
        $this->findOrCreatePaymentForPackageService->execute($orderPackage);
    }

    /**
     * Handle the OrderPackage "updated" event.
     *
     * @param OrderPackage $orderPackage
     * @return void
     */
    public function updated(OrderPackage $orderPackage): void
    {
        if ($orderPackage->cash_on_delivery >= 0) {
            $orderPackage->orderPayments()->update([
                'declared_sum' => $orderPackage->cash_on_delivery,
            ]);
        }
    }

    /**
     * Handle the OrderPackage "deleted" event.
     *
     * @param OrderPackage $orderPackage
     * @return void
     */
    public function deleted(OrderPackage $orderPackage): void
    {
        $orderPackage->orderPayments()->delete();
    }
}
