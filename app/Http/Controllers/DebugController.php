<?php

namespace App\Http\Controllers;

use App\Jobs\AllegroOrderSynchro;

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
