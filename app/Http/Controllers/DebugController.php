<?php

namespace App\Http\Controllers;


use App\Console\Commands\AnalyzeProductPrices;
use App\Entities\OrderAddress;
use App\Jobs\Cron\FinalProformConfirmationAutoApprovementJob;
use App\Jobs\Cron\SendInvoicesMailsJob;
use App\Jobs\FindNewAllegroOrders;
use App\Jobs\ImportOrdersFromSelloJob;
use App\Jobs\OrderProformSendMailJob;
use App\Jobs\Cron\SendFinalProformConfirmationMailsJob;
use App\Services\AllegroOrderService;
use Carbon\Carbon;

class DebugController extends Controller
{
    public function index()
    {
    	if (env('APP_ENV') != 'development') {
    		return;
	    }
    	
    	
    	$res = dispatch_now(new AnalyzeProductPrices());
    }
}
