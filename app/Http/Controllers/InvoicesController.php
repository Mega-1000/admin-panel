<?php

namespace App\Http\Controllers;

use App\Entities\BuyingInvoice;
use App\Entities\Order;
use App\Entities\OrderInvoice;
use App\Entities\SubiektInvoices;
use App\Http\Requests\AddInvoiceToOrder;
use App\Http\Requests\UploadInvoiceRequest;
use App\Services\Label\AddLabelService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvoicesController extends Controller
{

    public function getSubiektInvoice($id): BinaryFileResponse|RedirectResponse
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

    public function getInvoice($id): BinaryFileResponse|RedirectResponse
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

    public function addInvoice(AddInvoiceToOrder $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $order = Order::findOrFail($data['order_id']);
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            Storage::disk('local')->put('public/invoices/' . $filename, file_get_contents($file));
            $order->invoices()->create([
                'invoice_type' => $request->type,
                'invoice_name' => $filename
            ]);

            $arr = [];
            AddLabelService::addLabels($order, [263], $arr, []);

            return redirect()->back()->with(['message' => __('invoice.successfully_added'), 'alert-type' => 'success']);
        } catch (\Exception $exception) {
            error_log(print_r($exception->getMessage(), 1));
            return redirect()->route('orders.index')->with(['message' => __('invoice.not_added'),
                'alert-type' => 'error'
            ]);
        }
    }

    public function uploadInvoice(UploadInvoiceRequest $request): RedirectResponse
    {
        $files = $request->file('files');

        foreach ($files as $file) {
            $fileName = $file->getClientOriginalName();
            Storage::disk('invoicesDisk')->put($fileName, file_get_contents($file));
        }

        return redirect()->back()->with(['message' => __('invoice.successfully_added'), 'alert-type' => 'success']);
    }

    public function delete(): RedirectResponse
    {
        OrderInvoice::find(request()->query('id'))->delete();

        return redirect()->back();
    }

    public function deleteBuying(int $invoice): RedirectResponse
    {
        BuyingInvoice::find($invoice)->delete();

        return redirect()->back();
    }
}
