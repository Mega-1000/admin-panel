<?php

namespace App\Http\Controllers;


use App\Entities\OrderAddress;
use App\Jobs\FindNewAllegroOrders;
use App\Jobs\ImportOrdersFromSelloJob;
use App\Jobs\OrderProformSendMailJob;
use App\Jobs\Orders\CheckDeliveryAddressSendMailJob;
use App\Services\AllegroOrderService;

class DebugController extends Controller
{
    public function index()
    {
    	$oa = OrderAddress::find(1);
    	//$oa->firstname = 'test';
    	//$oa->save();
    	
	    dispatch_now(new CheckDeliveryAddressSendMailJob($oa->order));
    	dispatch(new OrderProformSendMailJob($oa->order, setting('allegro.address_changed_msg')));
	    
    	return;
	    
	    dispatch_now(new ImportOrdersFromSelloJob());
	    dispatch_now(new FindNewAllegroOrders());
	
	    $orderSerivce = new AllegroOrderService();
	    $orderSerivce->fixOrdersWithInvalidData();
    }
}
