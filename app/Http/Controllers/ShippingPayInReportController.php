<?php

namespace App\Http\Controllers;

use App\Entities\ShippingPayInReport;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingPayInReportController extends Controller
{
    public function __invoke(): View
    {
        return view('shipment-pay-in-report.index', [
            'report' => ShippingPayInReport::paginate(30),
        ]);
    }
}
