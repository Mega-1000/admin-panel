<?php

namespace App\Jobs\Orders;

use App\Entities\Order;
use App\Helpers\Helper;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Jobs\Job;
use App\Mail\MissingDeliveryAddressMail;
use App\Repositories\OrderRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class MissingDeliveryAddressSendMailJob extends Job implements ShouldQueue
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
        $hashedOrderId = Helper::encodeId($this->order->id);
        $formLink = rtrim(env('FRONT_NUXT_URL'),"/") . "/zamowienie/mozliwe-do-realizacji/brak-danych/{$hashedOrderId}";
        if (! $this->order->isDeliveryDataComplete()) {
            if(!empty($this->options)) {
                if(!empty($this->options["dispatch-labels-by-name"])) {
                    foreach ($this->options["dispatch-labels-by-name"] as $name) {
                        dispatch_now(new DispatchLabelEventByNameJob($this->order, $name));
                    }
                }
            }
            if (strpos($this->order->customer->login, 'allegromail.pl')) {
                return;
            }
            \Mailer::create()
                ->to($this->order->customer->login)
                ->send(new MissingDeliveryAddressMail("Niekompletne dane - numer zamówienia: {$this->order->id}", $formLink));
        }
    }
}
