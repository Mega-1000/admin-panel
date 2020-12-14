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

    /**
     * @return mixed
     */
    public function changeOrderInvoiceVisibility(int $invoiceId) {
        $orderInvoice = $this->orderInvoiceRepository->find($invoiceId);

        return $this->orderInvoiceRepository->update([
            'is_visible_for_client' => !$orderInvoice->is_visible_for_client,
        ], $invoiceId);
    }
}
