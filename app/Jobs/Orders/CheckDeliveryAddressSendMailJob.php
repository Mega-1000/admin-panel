<?php

namespace App\Jobs\Orders;

use App\Entities\Order;
use App\Jobs\Job;
use App\Mail\CheckDeliveryAddressMail;
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

	public function handle()
	{
		$formLink = rtrim(env('FRONT_NUXT_URL'),"/") . "/zamowienie/mozliwe-do-realizacji/brak-danych/{$this->order->id}";

		\Mailer::create()
			->to($this->order->customer->login)
			->send(new CheckDeliveryAddressMail("Sprawdz dane do dostawy i faktury - numer zamówienia: {$this->order->id}", $formLink));
	}
}
