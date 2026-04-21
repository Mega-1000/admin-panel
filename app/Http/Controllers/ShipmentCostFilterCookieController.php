<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipmentCostFilterCookieRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ShipmentCostFilterCookieController extends Controller
{
    public function __invoke(ShipmentCostFilterCookieRequest $request): RedirectResponse
    {
        ['from' => $from, 'to' => $to] = $request->validated();

        cookie()->queue('shipment_cost_filter', json_encode(['from' => $from, 'to' => $to]), 60 * 24 * 30);

        return redirect()->back();
    }
}
