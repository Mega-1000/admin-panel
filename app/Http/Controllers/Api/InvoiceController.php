<?php

namespace App\Http\Controllers\Api;

use App\Entities\Order;
use App\Http\Resources\InvoiceResource;
use Illuminate\Http\JsonResponse;
use App\Services\InvoiceService;

readonly class InvoiceController
{
    /**
     * @param InvoiceService $invoiceService
     */
    public function __construct(
        private InvoiceService $invoiceService,
    ) {
    }

    /**
     * @param Order $order
     *
     * @return JsonResponse
     */
    public function getInvoicesForOrder(Order $order): JsonResponse
    {
        $invoices = $this->invoiceService->getInvoicesForOrder($order);

        if ($invoices->isEmpty()) {
            return response()->json(['message' => 'No invoice file found for the order ID'], 404);
        }

        return response()->json(InvoiceResource::collection($invoices));
    }
}
