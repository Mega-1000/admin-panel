<?php

namespace App\Jobs\Cron;

use App\Entities\Label;
use App\Entities\OrderLabel;
use App\Jobs\AddLabelJob;
use App\Jobs\Job;
use App\Jobs\RemoveLabelJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Class FinalProformConfirmationAutoApprovementJob
 * @package App\Jobs
 */
class FinalProformConfirmationAutoApprovementJob extends Job implements ShouldQueue
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
	    if (!($orderLabels = OrderLabel::where('label_id', Label::FINAL_CONFIRMATION_SENDED)->with('order')->get())){
		    return;
	    }
	
	    $orderLabels->map(function ($orderLabel) {
		    dispatch_now(new RemoveLabelJob($orderLabel->order, [Label::FINAL_CONFIRMATION_SENDED]));
		    dispatch_now(new AddLabelJob($orderLabel->order, [Label::FINAL_CONFIRMATION_APPROVED]));
	    });
    }
}
