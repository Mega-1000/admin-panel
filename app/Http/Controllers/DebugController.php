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
    
        echo preg_replace('/[^0-9\+]+/', '', '+-$sdf45345');
   //     dispatch_now(new ImportOrdersFromSelloJob());
    	
    	$res = dispatch_now(new AllegroOrderSynchro());
    }
}
