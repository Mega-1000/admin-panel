<?php

namespace App\Jobs\Cron;

use App\Entities\Label;
use App\Entities\Order;
use App\Jobs\Job;
use App\Jobs\Orders\SendOrderInvoiceMsgMailJob;
use Carbon\Carbon;
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
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$orders = Order::whereHas('orderLabels', function($query){
			$query->where('label_id', Label::ORDER_ITEMS_REDEEMED_LABEL);
			$startOfDay = Carbon::now()->subDays(4)->startOfDay();
			$endOfDay = $startOfDay->copy()->endOfDay();
			$query->where('created_at', '>=', $startOfDay);
			$query->where('created_at', '<=', $endOfDay);
		})->whereDoesntHave('orderLabels', function($query){
			$query->where('label_id', Label::ORDER_INVOICE_MSG_SENDED);
			$query->where('label_id', Label::INVOICE_ISSUED_WITH_EXERTED_EFFECT);
		})->get();
		
		$orderCount = $orders->count();
		Log::info('Send order invoice msg. Orders total count: ' . $orderCount);
		
		if (!$orderCount) {
			return;
		}
		
		$orders->map(function ($order) {
			dispatch(new SendOrderInvoiceMsgMailJob($order));
		});
	}
}
