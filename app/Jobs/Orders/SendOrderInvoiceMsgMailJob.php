<?php

namespace App\Jobs\Orders;

use App\Entities\Order;
use App\Facades\Mailer;
use App\Helpers\EmailTagHandlerHelper;
use App\Jobs\Job;
use App\Mail\OrderMessageMail;
use App\Repositories\TagRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SendOrderInvoiceMsgMailJob extends Job implements ShouldQueue
{
    use IsMonitored, Queueable, SerializesModels;

    protected $order;

    /**
     * SendOrderInvoiceMsgMailJob constructor.
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
        $message = setting('allegro.order_invoice_msg');

        $subject = "Faktura za zakupy dokonane na allegro";

        $emailTagHandler->setOrder($this->order);

        foreach ($tags as $tag) {
            $method = $tag->handler;
            $message = preg_replace("[" . preg_quote($tag->name) . "]", $emailTagHandler->$method(), $message);
        }

        $pdf = Storage::disk('local')->path($this->order->proformStoragePath);

        Mailer::create()
            ->to($this->order->customer->login)
            ->send(new OrderMessageMail($subject, $message, $pdf));
    }
}
