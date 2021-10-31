<?php

namespace App\Jobs\Cron;

use App\Entities\Label;
use App\Entities\OrderLabel;
use App\Jobs\Job;
use App\Jobs\Orders\SendFinalProformConfirmationMailJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
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
		$orderLabels = OrderLabel::redeemed()->with('order')->get();
		if (!($orderCount = $orderLabels->count())) {
			Log::info('Send proform. Orders total count: 0');
			return;
		}
		
		Log::info('Send proform. Orders total count: ' . $orderCount);
		$processedCount = 0;
		$notFinalDayCount = 0;
		$hasProcessedLables = 0;
		$logOrderCount = 0;
		foreach ($orderLabels as $orderLabel) {
			if (!$orderLabel->order->isFinalConfirmationDay) {
				$notFinalDayCount++;
				continue;
			}
			
			if ($orderLabel->order->hasLabel(Label::REDEEMED_LABEL_PROCESSED_IDS)) {
				$hasProcessedLables++;
				if ($logOrderCount < 5) {
					Log::info('Send proform. Order with labels: ' . $orderLabel->order->id);
					$logOrderCount++;
				}
				
				continue;
			}
			
			$processedCount++;
			dispatch(new SendFinalProformConfirmationMailJob($orderLabel->order));
		}
		
		Log::info('Send proform. Orders processed count: ' . $processedCount);
		Log::info('Send proform. Orders not at final day count: ' . $notFinalDayCount);
		Log::info('Send proform. Orders processed labels count: ' . $hasProcessedLables);
	}
}
