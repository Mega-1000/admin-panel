<?php

namespace App\Jobs\Cron;

use App\Entities\Label;
use App\Entities\OrderLabel;
use App\Jobs\Job;
use App\Jobs\Orders\SendOrderInvoiceMsgMailJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Class SendOrderInvoiceMsgMailsJob
 * @package App\Jobs
 */
class SendOrderInvoiceMsgMailsJob extends Job implements ShouldQueue
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
		$orderCount = $orderLabels->count();
		Log::info('Send order invoice msg. Orders total count: ' . $orderCount);
		
		if (!$orderCount) {
			return;
		}
		
		$processedCount = 0;
		$notInvoiceDayCount = 0;
		$hasProcessedLables = 0;
		$logOrderCount = 0;
		foreach ($orderLabels as $orderLabel) {
			if (!$orderLabel->order->isInvoiceMsgDay) {
				$notInvoiceDayCount++;
				continue;
			}
			
			if ($orderLabel->order->hasLabel(Label::REDEEMED_LABEL_PROCESSED_IDS)) {
				$hasProcessedLables++;
				if ($logOrderCount < 5) {
					Log::info('Send order invoice msg. Order with labels: ' . $orderLabel->order->id);
					$logOrderCount++;
				}
				
				continue;
			}
			
			$processedCount++;
			dispatch(new SendOrderInvoiceMsgMailJob($orderLabel->order));
		}
		
		Log::info('Send order invoice msg. Orders processed count: ' . $processedCount);
		Log::info('Send order invoice msg. Orders not at invoice day count: ' . $notInvoiceDayCount);
		Log::info('Send order invoice msg. Orders processed labels count: ' . $hasProcessedLables);
	}
}
