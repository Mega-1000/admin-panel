<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Jobs\AllegroCustomerReturnsJob;
use App\Jobs\AllegroOrderSynchro;
use App\Jobs\CheckPackagesStatusJob;
use App\Jobs\GenerateXmlForNexoJob;

class DebugController extends Controller
{
    public function index()
    {
    	if (env('APP_ENV') == 'production') {
    		return;
	    }

        // CheckPackagesStatusJob::dispatchNow();
   //     dispatch_now(new ImportOrdersFromSelloJob());
//    	$res = dispatch_now(new AllegroCustomerReturnsJob());
    	$res = dispatch_now(new AllegroOrderSynchro());
    }
}
