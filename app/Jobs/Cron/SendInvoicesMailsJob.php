<?php

namespace App\Jobs\Cron;

use App\Entities\Label;
use App\Entities\Order;
use App\Jobs\Job;
use App\Jobs\Orders\SendInvoiceMailJob;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Class ImportCustomersJob
 * @package App\Jobs
 */
class SendInvoicesMailsJob extends Job implements ShouldQueue
{
    use Queueable, IsMonitored;


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
	    $orders = Order::where(function($query){
	    	$query->whereHas('orderLabels', function($query){
			    $query->where('label_id', Label::ORDER_RECEIVED_INVOICE_TODAY);
		    })->whereDoesntHave('orderLabels', function($query){
			    $query->where('label_id', Label::INVOICE_ISSUED_WITH_EXERTED_EFFECT);
		    });
	    })->orWhere(function($query){
	    	$query->whereHas('orderLabels', function($query){
			    $query->where('label_id', Label::ORDER_RECEIVED_INVOICE_STANDARD);
			
			    $startOfDay = Carbon::now()->subDays(15)->startOfDay();
			    $endOfDay = $startOfDay->copy()->endOfDay();
			    $query->where('created_at', '>=', $startOfDay);
			    $query->where('created_at', '<=', $endOfDay);
		    })->whereDoesntHave('orderLabels', function($query){
			    $query->where('label_id', Label::INVOICE_ISSUED_WITH_EXERTED_EFFECT);
		    });
	    })->get();
		   
	    $orders->map(function ($order) {
		    dispatch_now(new SendInvoiceMailJob($order));
	    });
    }
}
