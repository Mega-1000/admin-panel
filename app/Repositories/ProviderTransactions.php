<?php

namespace App\Repositories;

use App\Entities\ProviderTransaction;

readonly final class ProviderTransactions
{
    /**
     * Get provider transaction by provider and invoice number and cash on delivery.
     *
     * @param string $provider
     * @param string $invoiceNumber
     * @param string $cashOnDelivery
     * @return ProviderTransaction|null
     */
    public static function getProviderTransactionByProviderAndInvoiceNumberAndCashOnDelivery(string $provider, string $invoiceNumber, string $cashOnDelivery): ?ProviderTransaction
    {
        return ProviderTransaction::where('provider', $provider)
            ->where('invoice_number', $invoiceNumber)
            ->where('cash_on_delivery', $cashOnDelivery)
            ->first();
    }
}
