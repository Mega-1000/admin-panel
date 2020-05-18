<?php

namespace App\Http\Controllers\Api;

use App\Entities\SubiektInvoices;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvoicesController extends Controller
{
    use ApiResponsesTrait;

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
}
