<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class TableOfShipmentPaymentsErrorsController extends Controller
{
    public function __invoke(): View
    {
        return view('shipment-payments-errors.table-of-shipment-payments-errors', [
            ]);
    }
}
