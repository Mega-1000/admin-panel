<?php

namespace App\Jobs\Cron;

use App\Entities\Label;
use App\Entities\OrderLabel;
use App\Jobs\Job;
use App\Jobs\Orders\SendFinalProformConfirmationMailJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Class SendFinalProformConfirmationMailsJob
 * @package App\Jobs
 */
class SendFinalProformConfirmationMailsJob extends Job implements ShouldQueue
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
	    if (!($orderLabels = OrderLabel::redeemed()->with('order')->get())){
		    return;
	    }
	
	    $orderLabels->map(function ($orderLabel) {
		    if ($orderLabel->order->isFinalConfirmationDay && !$orderLabel->order->hasLabel(Label::REDEEMED_LABEL_PROCESSED_IDS)) {
			    dispatch(new SendFinalProformConfirmationMailJob($orderLabel->order));
		    }
	    });
    }
}
