<?php

namespace App\Services;

use App\DTO\Invoices\InvoiceDTO;
use App\Entities\Order;
use App\Repositories\InvoiceRepositoryInterface;
use Illuminate\Support\Collection;

readonly class InvoiceService
{

    /**
     * @param InvoiceRepositoryInterface $invoiceRepository
     */
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
    ) {
    }

    /**
     * @param Order $order
     *
     * @return Collection
     */
    public function getInvoicesForOrder(Order $order): \Illuminate\Support\Collection
    {
        $invoices = $this->invoiceRepository->getInvoicesForOrder($order);
        $formattedInvoices = [];

        foreach ($invoices as $invoice) {
            $formattedInvoices[] = [
                'name' => $invoice->getFileName(),
                'url' => $invoice->getUrl(),
            ];
        }

        return collect($formattedInvoices)->map(fn ($invoice) => new InvoiceDTO($invoice['name'], $invoice['url']));
    }
}
