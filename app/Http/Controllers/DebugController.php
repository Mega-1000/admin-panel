<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Jobs\AllegroCustomerReturnsJob;
use App\Jobs\AllegroOrderSynchro;
use App\Jobs\ChangeShipmentDatePackagesJob;
use App\Jobs\CheckPackagesStatusJob;
use App\Jobs\GenerateXmlForNexoJob;
use App\Jobs\UpdatePackageRealCostJob;

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
//    	$res = dispatch_now(new CheckPackagesStatusJob());
//    	$res = dispatch_now(new CheckPackagesStatusJob());
//    	$res = dispatch_now(new ChangeShipmentDatePackagesJob());
   	// $res = dispatch_now(new AllegroOrderSynchro());
    	$res = dispatch_now(new GenerateXmlForNexoJob());
    }
}
