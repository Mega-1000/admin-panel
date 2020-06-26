<?php

namespace App\Http\Controllers;

use App\Entities\Deliverer;
use Illuminate\Http\Request;

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
            error_log('test3');
            $deliverer = Deliverer::findOrFail($request->id);
            error_log('test2');
            $deliverer->delete();
            error_log('test');
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
