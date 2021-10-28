<?php

namespace App\Jobs\Cron;

use App\Entities\Label;
use App\Entities\OrderLabel;
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
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
	    $orderLabels = OrderLabel::where('label_id', Label::ORDER_RECEIVED_INVOICE_TODAY)->with('order')->get();
	    if ($orderLabels->count()) {
		    $orderLabels->map(function ($orderLabel) {
			    if (!$orderLabel->order->hasLabel(Label::INVOICE_ISSUED_WITH_EXERTED_EFFECT)) {
				    dispatch_now(new SendInvoiceMailJob($orderLabel->order));
			    }
		    });
	    }
	
	    $orderLabels = OrderLabel::where('label_id', Label::ORDER_RECEIVED_INVOICE_STANDARD)->with('order')->get();
	    if ($orderLabels->count()) {
		    $startOfDay = Carbon::now()->subDays(15)->startOfDay();
		    $endOfDay = $startOfDay->copy()->endOfDay();
		
		    foreach ($orderLabels as $orderLabel) {
			    if (!$orderLabel->order->hasLabel(Label::INVOICE_ISSUED_WITH_EXERTED_EFFECT)) {
				    if ($orderLabel->created_at >= $startOfDay && $orderLabel->created_at <= $endOfDay) {
					    dispatch_now(new SendInvoiceMailJob($orderLabel->order));
				    }
			    }
		    };
	    }
    }
}
