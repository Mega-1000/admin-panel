<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\OrderInvoiceRepository;
use Illuminate\Http\Response;

class OrderInvoiceService
{
    protected $orderInvoiceRepository;

    public function __construct(OrderInvoiceRepository $orderInvoiceRepository)
    {
        $this->orderInvoiceRepository = $orderInvoiceRepository;
    }

    public function changeOrderInvoiceVisibility(int $invoiceId): Response {
        $orderInvoice = $this->orderInvoiceRepository->find($invoiceId);

        $orderInvoice = $this->orderInvoiceRepository->update([
            'is_visible_for_client' => !$orderInvoice->is_visible_for_client,
        ], $invoiceId);

        return response(['invoice_name' => $orderInvoice->invoice_name]);
    }
}
