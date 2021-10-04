<?php

namespace App\Http\Controllers;


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
    	
    	$oa = OrderAddress::find(1);
    	//$oa->firstname = 'test';
    	//$oa->save();
    	$o = $oa->order;
    	$l = $o->labels;
	    return;
	    dispatch_now(new SendFinalProformConfirmationMailsJob());
	    dispatch_now(new FinalProformConfirmationAutoApprovementJob());
	    dispatch_now(new SendInvoicesMailsJob());
    	return;
    	
	    dispatch(new OrderProformSendMailJob($oa->order, setting('allegro.address_changed_msg')));
	    
	    dispatch_now(new ImportOrdersFromSelloJob());
	    dispatch_now(new FindNewAllegroOrders());
	
	    $orderSerivce = new AllegroOrderService();
	    $orderSerivce->fixOrdersWithInvalidData();
    }
}
