<?php

namespace App\Http\Controllers;

use App\Jobs\AllegroOrderSynchro;
use App\Jobs\CheckPackagesStatusJob;

class DebugController extends Controller
{
    public function index()
    {
    	if (env('APP_ENV') == 'production') {
    		return;
	    }
    
        // CheckPackagesStatusJob::dispatchNow();
   //     dispatch_now(new ImportOrdersFromSelloJob());
    	$res = dispatch_now(new AllegroOrderSynchro());
    }
}
