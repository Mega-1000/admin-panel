<?php

namespace App\Jobs\Orders;

use App\Entities\Order;
use App\Helpers\EmailTagHandlerHelper;
use App\Jobs\Job;
use App\Mail\OrderMessageMail;
use App\Repositories\TagRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SendItemsConstructedMailJob extends Job implements ShouldQueue
{
	use IsMonitored, Queueable, SerializesModels;

	protected $order;

	/**
	 * SendItemsConstructedMailJob constructor.
	 * @param $order
	 */
	public function __construct(Order $order)
	{
		$this->order = $order;
	}

	public function handle(EmailTagHandlerHelper $emailTagHandler, TagRepository $tagRepository)
	{
		$tags = $tagRepository->all();
		if ($this->order->sello_id) {
			$message = setting('allegro.order_items_constructed_msg');
		} else {
			$message = setting('site.order_items_constructed_msg');
		}
		
		$subject = "Państwa oferta zostałą przygotowana i oczekuje na odbior przez kuriera";
		
		$emailTagHandler->setOrder($this->order);
		
		foreach ($tags as $tag) {
			$method = $tag->handler;
			$message = preg_replace("[" . preg_quote($tag->name) . "]", $emailTagHandler->$method(), $message);
		}
		
		\Mailer::create()
			->to($this->order->customer->login)
			->send(new OrderMessageMail($subject, $message));
	}
}
