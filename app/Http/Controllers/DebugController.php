<?php

namespace App\Http\Controllers;


use App\Jobs\FindNewAllegroOrders;
use App\Jobs\ImportOrdersFromSelloJob;
use App\Services\AllegroOrderService;

class DebugController extends Controller
{
    public function index()
    {
	    dispatch_now(new ImportOrdersFromSelloJob());
	    dispatch_now(new FindNewAllegroOrders());
	
	    $orderSerivce = new AllegroOrderService();
	    $orderSerivce->fixOrdersWithInvalidData();
    }
}
