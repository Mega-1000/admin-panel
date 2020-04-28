<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Repositories\StatusRepository;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

use App\Repositories\OrderRepository;
use App\Repositories\TagRepository;
use App\Helpers\EmailTagHandlerHelper;
use App\Mail\OrderStatusChanged;


/**
 * Class OrderStatusChangedNotificationJob
 * @package App\Jobs
 */
class OrderStatusChangedNotificationJob extends Job
{

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
        $tags = $tagRepository->all();
        $oldStatus = $statusRepository->find($this->oldStatus);

        if ($this->message !== null) {
            $message = $this->message;
        } else {
            $message = $order->status->message;
        }

        $emailTagHandler->setOrder($order);

        foreach ($tags as $tag) {
            $method = $tag->handler;
            $message = preg_replace("[" . preg_quote($tag->name) . "]", $emailTagHandler->$method(), $message);
        }
        if ($order->status_id == 3 || $order->status_id == 4) {
            $subject = "Zmiana statusu - numer oferty: " . $this->orderId . " z: " . $oldStatus->name . " na: " . $order->status->name . ' oraz proforma';
            $order = Order::find($order->id);
            $proformDate = \Carbon\Carbon::now()->format('m-Y');
            $proformDate = str_replace('-', '/', $proformDate);
            $date = \Carbon\Carbon::now()->toDateString();
            $order->proforma_date = $date;
            $order->save();

            $pdf = PDF::loadView('pdf.proform', compact('date', 'proformDate', 'order'));
            \Mailer::create()
                ->to($order->customer->login)
                ->send(new OrderStatusChanged($subject, $message, $pdf->output()));
        }
    }
}
