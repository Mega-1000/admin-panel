<?php

namespace App\Jobs\Cron;

use App\Entities\Label;
use App\Entities\OrderLabel;
use App\Jobs\Job;
use App\Jobs\Orders\SendInvoiceMailJob;
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
	    if (!($orderLabels = OrderLabel::where('label_id', Label::FINAL_CONFIRMATION_APPROVED)->with('order')->get())){
		    return;
	    }
	
	    $orderLabels->map(function ($orderLabel) {
		    if (!$orderLabel->order->hasLabel(Label::INVOICE_SENDED)) {
			    dispatch_now(new SendInvoiceMailJob($orderLabel->order));
		    }
	    });
    }
}
