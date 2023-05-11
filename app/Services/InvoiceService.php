<?php

namespace App\Services;

use App\DTO\Invoices\InvoiceDTO;
use App\Entities\Order;
use App\Repositories\FileInvoiceRepository;
use Illuminate\Support\Collection;

readonly class InvoiceService
{

    /**
     * @param FileInvoiceRepository $invoiceRepository
     */
    public function __construct(
        private FileInvoiceRepository $invoiceRepository,
    ) {
    }

    /**
     * @param Order $order
     *
     * @return Collection
     */
    public function getInvoicesForOrder(Order $order): Collection
    {
        $invoices = $this->invoiceRepository->getInvoicesForOrder($order);
        $formattedInvoices = [];

        foreach ($invoices as $invoice) {
            $formattedInvoices[] = [
                'name' => $invoice->getFileName(),
                'url' => asset($invoice->getUrl()),
            ];
        }

        return collect($formattedInvoices)->map(fn ($invoice) => new InvoiceDTO($invoice['name'], $invoice['url']));
    }
}
