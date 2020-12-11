<?php

namespace App\Http\Controllers\Api;

use App\Entities\SubiektInvoices;
use App\Http\Controllers\Controller;
use App\Services\OrderInvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvoicesController extends Controller
{
    use ApiResponsesTrait;

    protected $orderInvoiceService;

    public function __construct(OrderInvoiceService $orderInvoiceService)
    {
        $this->orderInvoiceService = $orderInvoiceService;
    }

    public function getInvoice(Request $request, $id)
    {
        try {
            $invoice = SubiektInvoices::findOrFail($id);
            $user = Auth::user();
            if ($invoice->order->customer_id != $user->id) {
                throw new \Exception('wrong user');
            }
            $item = Storage::path("user-files/invoices/$invoice->ftp_invoice_filename");
            return response()->file($item);
        } catch (\Exception $exception) {
            return $this->notFoundResponse("Invoice not found");
        }
    }

    public function changeInvoiceVisibility(int $invoiceId): JsonResponse
    {
        $orderInvoice = $this->orderInvoiceService->changeOrderInvoiceVisibility($invoiceId);

        return response()->json(['invoice_name' => $orderInvoice->invoice_name]);
    }
}
