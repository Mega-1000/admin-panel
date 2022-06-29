<?php

namespace App\Http\Controllers;


use App\Console\Commands\AnalyzeProductPrices;
use App\Entities\OrderAddress;
use App\Jobs\AllegroOrderSynchro;
use App\Jobs\Cron\SendInvoicesMailsJob;
use App\Jobs\FindNewAllegroOrders;
use App\Jobs\ImportOrdersFromAllegroJob;
use App\Jobs\ImportOrdersFromSelloJob;
use App\Jobs\OrderProformSendMailJob;

use App\Services\AllegroOrderService;
use Carbon\Carbon;

class DebugController extends Controller
{
    public function index()
    {
    	if (env('APP_ENV') != 'development') {
    		return;
	    }
    
     
   //     dispatch_now(new ImportOrdersFromSelloJob());
    	
    	$res = dispatch_now(new AllegroOrderSynchro());
    }
}
