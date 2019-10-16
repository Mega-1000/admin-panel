<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

use App\Repositories\OrderRepository;
use App\Repositories\TagRepository;
use App\Repositories\LabelRepository;
use App\Helpers\EmailTagHandlerHelper;
use App\Mail\LabelAdd;

class LabelAddNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
    * @var
    */
   protected $orderId;

   /**
    * @var
    */
   protected $labelId;

   /**
    * Requires to pass id of Order that's status changed to ::dispatch()
    *
    * @param $orderId
    */
   public function __construct($orderId, $labelId)
   {
       $this->orderId = $orderId;
       $this->labelId = $labelId;
   }

   /**
    * Execute the job.
    *
    * @return void
    */
   public function handle(EmailTagHandlerHelper $emailTagHandler, OrderRepository $orderRepository, LabelRepository $labelRepository, TagRepository $tagRepository)
   {
       $order = $orderRepository->find($this->orderId);
       $tags = $tagRepository->all();
       $label = $labelRepository->find($this->labelId);

       if($label->message !== null){
           $message = $label->message;
       } else {
           $message = '';
       }

       $emailTagHandler->setOrder($order);

       foreach($tags as $tag) {
           $method = $tag->handler;
           $message = preg_replace("[" . preg_quote($tag->name) . "]", $emailTagHandler->$method(), $message);
       }

       $subject = "Mega1000 - zmieniono status zamówienia: " . $this->orderId . ' na status: ' . $label->name;
       \Mailer::create()
           ->to($order->customer->login)
           ->send(new LabelAdd($subject, $message));
   }
}
