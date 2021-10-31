<?php

namespace App\Jobs\Orders;

use App\Entities\Label;
use App\Entities\Order;
use App\Helpers\EmailTagHandlerHelper;
use App\Jobs\AddLabelJob;
use App\Jobs\Job;
use App\Mail\InvoiceSent;
use App\Repositories\TagRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SendInvoiceMailJob extends Job implements ShouldQueue
{
	use IsMonitored, Queueable, SerializesModels;

	protected $order;

	/**
	 * SendInvoiceJob constructor.
	 * @param $order
	 */
	public function __construct(Order $order)
	{
		$this->order = $order;
	}

	public function handle(EmailTagHandlerHelper $emailTagHandler, TagRepository $tagRepository)
	{
		if (!($invoiceRow = DB::table('gt_invoices')->where('order_id', $this->order->id)->where('gt_invoice_status_id', '18')->first())) {
			return;
		}
		
		$tags = $tagRepository->all();
		$message = "";
		
		$subject = "Faktura Mega1000 - numer zamówienia: {$this->order->id}";
		
		$emailTagHandler->setOrder($this->order);
		
		foreach ($tags as $tag) {
			$method = $tag->handler;
			$message = preg_replace("[" . preg_quote($tag->name) . "]", $emailTagHandler->$method(), $message);
		}
		
		\Mailer::create()
			->to($this->order->customer->login)
			->send(new InvoiceSent($subject, $message, $invoiceRow->ftp_invoice_filename));

		dispatch_now(new AddLabelJob($this->order, [Label::INVOICE_SENDED]));
	}
}
