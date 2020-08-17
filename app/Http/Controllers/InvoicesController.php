<?php

namespace App\Http\Controllers;

use App\Entities\OrderInvoice;
use App\Entities\SubiektInvoices;
use Illuminate\Http\Request;
use App\Entities\PackingType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvoicesController extends Controller
{

    public function getSubiektInvoice($id)
    {
        try {
            $invoice = SubiektInvoices::findOrFail($id);
            $item = Storage::path("user-files/invoices/$invoice->ftp_invoice_filename");
            return response()->file($item);
        } catch (\Exception $exception) {
            return redirect()->route('orders.index')->with(['message' => __('invoice.not_found'),
                'alert-type' => 'error'
            ]);
        }
    }

    public function getInvoice($id)
    {
        try {
            $invoice = OrderInvoice::findOrFail($id);
            $item = Storage::path("public/invoices/$invoice->invoice_name");
            return response()->file($item);
        } catch (\Exception $exception) {
            return redirect()->route('orders.index')->with(['message' => __('invoice.not_found'),
                'alert-type' => 'error'
            ]);
        }
    }

}
