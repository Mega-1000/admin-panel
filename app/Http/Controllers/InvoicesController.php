<?php

namespace App\Http\Controllers;

use App\Entities\BuyingInvoice;
use App\Entities\Order;
use App\Entities\OrderInvoice;
use App\Entities\SubiektInvoices;
use App\Facades\Mailer;
use App\Http\Requests\AddInvoiceToOrder;
use App\Http\Requests\UploadInvoiceRequest;
use App\Mail\invoiceInAccountMail;
use App\Services\Label\AddLabelService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Spatie\PdfToText\Pdf;

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

    public function uploadInvoice(UploadInvoiceRequest $request): mixed
    {
        $files = $request->file('files');

        // get file content to text it is pdf file
        foreach ($files as $file) {
            $fileName = $file->getClientOriginalName();
            $fileContent = file_get_contents($file);

            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($file);
            $text = $pdf->getText();
            $text = $this->cleanText($text);

            preg_match('/Uwagi:(.{1,5})/u', $text, $matches);

            if (empty($matches)) {
                return response()->json('W jednej z faktur nie znaleziono numeru zamówienia' . ' lub nie znaleziono tekstu "Uwagi:" ogarnij to i wróć');
            }

            $orderId = $matches[0];

            Storage::disk('invoicesDisk')->put($orderId . $fileName, file_get_contents($file));

            $order = Order::find($orderId);

            $arr = [];
            AddLabelService::addLabels($order, [193], $arr, []);

            Mailer::create()
                ->to($order->customer->login)
                ->send(new invoiceInAccountMail($order));
        }

        return redirect()->back()->with(['message' => __('invoice.successfully_added'), 'alert-type' => 'success']);
    }

    private function cleanText($text): string
    {
        // Remove non-UTF8 characters
        $text = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $text);

        // Convert to UTF-8 if not already
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'ASCII,UTF-8,ISO-8859-1');
        }

        // Remove any remaining invalid UTF-8 sequences
        $text = iconv('UTF-8', 'UTF-8//IGNORE', $text);

        // Trim whitespace
        $text = trim($text);

        return $text;
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
