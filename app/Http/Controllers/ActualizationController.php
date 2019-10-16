<?php

namespace App\Http\Controllers;

use App\Jobs\CheckPriceChangesInProductsJob;
use Illuminate\Http\Request;

class ActualizationController extends Controller
{

    public function sendActualization()
    {
        dispatch_now(new CheckPriceChangesInProductsJob('all'));

        return redirect()->route('orders.index')->with([
            'message' => __('Prośba o aktualizację cen została wysłana.'),
            'alert-type' => 'success'
        ]);
    }
}
