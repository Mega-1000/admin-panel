<?php

namespace App\Jobs\Orders;

use App\Entities\Order;
use App\Jobs\Job;
use App\Mail\CheckDeliveryAddressMail;
use App\Repositories\OrderRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class CheckDeliveryAddressSendMailJob extends Job implements ShouldQueue
{
	use IsMonitored;
	
	protected $order;
	protected $options;
	
	/**
	 * MissingDeliveryAddressSendMailJob constructor.
	 * @param $order
	 */
	public function __construct($order, $options = [])
	{
		$this->order = $order;
		$this->options = $options;
	}
	
	public function handle(OrderRepository $orderRepository)
	{
		if (! ($this->order instanceof Order)) {
			$this->order = $orderRepository->find($this->order);
		}
		
		if (strpos($this->order->customer->login, 'allegromail.pl')) {
			return;
		}
		
		$formLink = env('FRONT_NUXT_URL') . "/zamowienie/mozliwe-do-realizacji/brak-danych/{$this->order->id}";
		
		\Mailer::create()
			->to($this->order->customer->login)
			->send(new CheckDeliveryAddressMail("Sprawdz dane do dostawy i faktury - numer zamówienia: {$this->order->id}", $formLink));
	}
}
