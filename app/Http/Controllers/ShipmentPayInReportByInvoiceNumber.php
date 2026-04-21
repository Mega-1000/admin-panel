<?php

namespace App\Http\Controllers;

use App\Entities\ShippingPayInReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentPayInReportByInvoiceNumber extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(
            ShippingPayInReport::where('nr_faktury_do_ktorej_dany_lp_zostal_przydzielony', $request->invoice_number)
                ->select(array_diff(ShippingPayInReport::getColumns(), ['reszta']))
                ->get()
                ->toArray()
        );
    }
}
