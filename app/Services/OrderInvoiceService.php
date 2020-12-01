<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\OrderInvoiceRepository;

class OrderInvoiceService
{
    protected $orderInvoiceRepository;

    public function __construct(OrderInvoiceRepository $orderInvoiceRepository)
    {
        $this->orderInvoiceRepository = $orderInvoiceRepository;
    }

    public function changeOrderInvoiceVisibility(int $invoiceId): void {
        $orderInvoice = $this->orderInvoiceRepository->find($invoiceId);

        if($orderInvoice->is_visible_for_client === null || $orderInvoice->is_visible_for_client === true) {
            $visibility = false;
        } else {
            $visibility = true;
        }

        $this->orderInvoiceRepository->update([
            'is_visible_for_client' => $visibility
        ],
            $invoiceId);
    }
}
