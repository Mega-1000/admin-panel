<?php

namespace App\Jobs\Orders;

use App\Entities\Order;
use App\Helpers\EmailTagHandlerHelper;
use App\Jobs\Job;
use App\Mail\CheckDeliveryAddressMail;
use App\Repositories\TagRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class CheckDeliveryAddressSendMailJob extends Job implements ShouldQueue
{
	use IsMonitored, Queueable, SerializesModels;

	protected $order;

	/**
	 * CheckDeliveryAddressSendMailJob constructor.
	 * @param $order
	 */
	public function __construct(Order $order)
	{
		$this->order = $order;
	}

	public function handle(EmailTagHandlerHelper $emailTagHandler, TagRepository $tagRepository)
	{
		$tags = $tagRepository->all();
		$message = $this->order->sello_id
			? setting('allegro.check_address_msg')
			: setting('site.check_address_msg');
		
		$subject = "Sprawdz dane do dostawy i faktury - numer zamówienia: {$this->order->id}";
		
		$emailTagHandler->setOrder($this->order);
		
		foreach ($tags as $tag) {
			$method = $tag->handler;
			$message = preg_replace("[" . preg_quote($tag->name) . "]", $emailTagHandler->$method(), $message);
		}
		
		try {
			\Mailer::create()
				->to($this->order->customer->login)
				->send(new CheckDeliveryAddressMail($subject, $message));
		} catch (\Exception $e) {
			\Log::error('Mailer can\'t send email', ['message' => $e->getMessage(), 'path' => $e->getTraceAsString()]);
		}
	}
}
