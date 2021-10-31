<?php

namespace App\Jobs\Orders;

use App\Entities\Label;
use App\Entities\Order;
use App\Helpers\EmailTagHandlerHelper;
use App\Jobs\AddLabelJob;
use App\Jobs\Job;
use App\Mail\OrderMessageMail;
use App\Repositories\TagRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SendFinalProformConfirmationMailJob extends Job implements ShouldQueue
{
	use IsMonitored, Queueable, SerializesModels;

	protected $order;

	/**
	 * SendFinalProformConfirmationMailJob constructor.
	 * @param $order
	 */
	public function __construct(Order $order)
	{
		$this->order = $order;
	}

	public function handle(EmailTagHandlerHelper $emailTagHandler, TagRepository $tagRepository)
	{
		dispatch_now(new GenerateOrderProformJob($this->order));
		
		$tags = $tagRepository->all();
		if ($this->order->sello_id) {
			$message = setting('allegro.final_confirmation_msg');
		} else {
			$message = setting('site.final_confirmation_msg');
		}
		
		$subject = "Prosbe o ostateczne potwierdzenie zgodnosci oferty pod wzlgdedem danych i asortymentu - numer zamówienia: {$this->order->id}";
		
		$emailTagHandler->setOrder($this->order);
		
		foreach ($tags as $tag) {
			$method = $tag->handler;
			$message = preg_replace("[" . preg_quote($tag->name) . "]", $emailTagHandler->$method(), $message);
		}
		
		$pdf = Storage::disk('local')->get($this->order->proformStoragePath);
		
		\Mailer::create()
			->to($this->order->customer->login)
			->send(new OrderMessageMail($subject, $message, $pdf));
		dispatch_now(new AddLabelJob($this->order->id, [Label::FINAL_CONFIRMATION_SENDED]));
	}
}
