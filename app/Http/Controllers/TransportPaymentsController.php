<?php

namespace App\Http\Controllers;

use App\Entities\Deliverer;
use App\Helpers\transportPayments\TransportPaymentImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TransportPaymentsController extends Controller
{
    public function list()
    {
        return view('transport.list', ['deliverers' => Deliverer::all()]);
    }

    public function createOrUpdate(Request $request)
    {
        try {
            $deliverer = false;
            if ($request->id) {
                $deliverer = Deliverer::findOrFail($request->id);
            }
            return view('transport.single', ['deliverer' => $deliverer]);
        } catch (\Exception $e) {
            return redirect()->route('transportPayment.list')->with([
                'message' => __('transport.errors.not-found'),
                'alert-type' => 'error'
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {
            $deliverer = Deliverer::findOrFail($request->id);
            $deliverer->delete();
            return redirect()->route('transportPayment.list')->with([
                'message' => __('voyager.generic.successfully_deleted'),
                'alert-type' => 'success'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('transportPayment.list')->with([
                'message' => __('transport.errors.not-found'),
                'alert-type' => 'error'
            ]);
        }
    }

    public function updatePricing(Request $request)
    {
        $file = $request->file('file');
        $maxFileSize = 20000000;
        if ($file->getSize() > $maxFileSize) {
            return redirect()->route('orders.index')->with([
                'message' => __('transport.errors.too-big-file'),
                'alert-type' => 'error'
            ]);
        }

        do {
            $fileName = Str::random(40) . '.csv';
            $path = Storage::path('user-files/transport/') . $fileName;
        } while (file_exists($path));

        $file->move(Storage::path('user-files/transport/'), $fileName);

        try {
            $deliverer = Deliverer::findOrFail($request)->first();
            $importer = new TransportPaymentImporter();
            $importer->setColumnNetPayment($deliverer->net_payment_column_number)
                ->setColumnGrossPayment($deliverer->gross_payment_column_number_gross)
                ->setColumnLetter($deliverer->letter_number_column_number);
            $errors = $importer->import($fileName);
        } catch (\Exception $e) {
            return redirect()->route('orders.index')->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
        return redirect()->route('orders.index')->with(
            'update_errors', $errors
        );
    }

    public function store(Request $request)
    {
        if ($request->gross_payment_column_number_gross) {
            $request->validate([
                'name' => 'required',
                'gross_payment_column_number_gross' => 'numeric',
                'letter_number_column_number' => 'numeric',
            ]);
        } else if ($request->net_payment_column_number) {
            $request->validate([
                'name' => 'required|max:255',
                'net_payment_column_number' => 'numeric',
                'letter_number_column_number' => 'numeric',
            ]);
        } else {
            $request->validate([
                'name' => 'required',
                'net_payment_column_number' => 'numeric',
                'gross_payment_column_number_gross' => 'numeric',
                'letter_number_column_number' => 'numeric',
            ]);
        }
        $deliverer = Deliverer::find($request->id);
        if ($deliverer) {
            $deliverer->update($request->all());
            return redirect()->route('transportPayment.list')->with([
                'message' => __('voyager.generic.successfully_updated'),
                'alert-type' => 'success'
            ]);
        } else {
            Deliverer::create($request->all());
            return redirect()->route('transportPayment.list')->with([
                'message' => __('voyager.generic.successfully_added_new'),
                'alert-type' => 'success'
            ]);
        }
    }
}
