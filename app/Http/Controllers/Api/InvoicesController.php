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
        error_log('test');
        try {
            $invoice = SubiektInvoices::findOrFail($id);
            $user = Auth::user();
        error_log($user->id);
        error_log('test2');
        error_log($invoice->order->customer_id);
            if ($invoice->order->customer_id != $user->id) {
                throw new \Exception('wrong user');
            }
        error_log('test3');
            $item = Storage::path("user-files/invoices/$invoice->ftp_invoice_filename");
            return response()->file($item);
        } catch (\Exception $exception) {
            return $this->notFoundResponse("Invoice not found");
        }
    }
}
