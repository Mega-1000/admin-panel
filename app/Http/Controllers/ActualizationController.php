<?php

namespace App\Http\Controllers;

use App\Jobs\CheckPriceChangesInProductsJob;

class ActualizationController extends Controller
{

    public function sendActualization()
    {
        dispatch_now(new CheckPriceChangesInProductsJob(date('Y-m-d', time() + 3600 * 24 * 365 * 10)));

        return redirect()->route('orders.index')->with([
            'message' => __('Prośba o aktualizację cen została wysłana.'),
            'alert-type' => 'success'
        ]);
    }
}
