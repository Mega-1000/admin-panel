<?php

namespace App\Jobs;

use App\Helpers\EmailTagHandlerHelper;
use App\Mail\OrderStatusChanged;
use App\Repositories\OrderRepository;
use App\Repositories\StatusRepository;
use App\Repositories\TagRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


/**
 * Class OrderStatusChangedNotificationJob
 * @package App\Jobs
 */
class OrderStatusChangedNotificationJob extends Job implements ShouldQueue
{
	use Queueable;
    /**
     * @var
     */
    protected $orderId;

    /**
     * @var null
     */
    protected $message;

    /**
     * @var null
     */
    protected $oldStatus;

    /**
     * Requires to pass id of Order that's status changed to ::dispatch()
     *
     * @param $orderId
     * @param $message
     * @param $oldStatus
     */
    public function __construct($orderId, $message = null, $oldStatus = null)
    {
        $this->orderId = $orderId;
        $this->message = $message;
        $this->oldStatus = $oldStatus;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(EmailTagHandlerHelper $emailTagHandler, OrderRepository $orderRepository, TagRepository $tagRepository, StatusRepository $statusRepository)
    {
	    $order = $orderRepository->find($this->orderId);
//    	if ($order->status_id != 3 || $order->status_id != 4) {
//    		return;
//	    }

        $tags = $tagRepository->all();
        $oldStatus = $statusRepository->find($this->oldStatus);

        $message = $this->message !== null ? $this->message : $order->status->message;

        $emailTagHandler->setOrder($order);

        foreach ($tags as $tag) {
            $method = $tag->handler;
            $message = preg_replace("[" . preg_quote($tag->name) . "]", $emailTagHandler->$method(), $message);
        }

        $subject = "Zmiana statusu - numer oferty: " . $this->orderId . " z: " . $oldStatus->name . " na: " . $order->status->name;

        $mail_to = $order->customer->login;

        try {
            \Mailer::create()
                ->to($mail_to)
                ->send(new OrderStatusChanged($subject, $message));
        } catch (\Exception $e) {
            \Log::error('Mailer can\'t send email', ['message' => $e->getMessage(), 'path' => $e->getTraceAsString()]);
        }
    }
}
