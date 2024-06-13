<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Entities\OrderOffer;
use App\Entities\Status;
use App\Entities\Tag;
use App\Facades\Mailer;
use App\Helpers\EmailTagHandlerHelper;
use App\Jobs\Orders\GenerateOrderProformJob;
use App\Mail\OrderStatusChanged;
use App\Repositories\OrderRepository;
use App\Repositories\StatusRepository;
use App\Repositories\TagRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


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
     * Execute the job.z
     *
     * @param EmailTagHandlerHelper $emailTagHandler
     * @param OrderRepository $orderRepository
     * @param TagRepository $tagRepository
     * @param StatusRepository $statusRepository
     * @return void
     */
    public function handle(
        EmailTagHandlerHelper $emailTagHandler,
        OrderRepository $orderRepository,
        TagRepository $tagRepository,
        StatusRepository $statusRepository
    ): void
    {
        $order = Order::find($this->orderId);

        $tags = Tag::all();
        $oldStatus = Status::find($this->oldStatus);

        $message = $this->message !== null ? $this->message : $order->status->message;

        $emailTagHandler->setOrder($order);

        foreach ($tags as $tag) {
            $method = $tag->handler;
            $message = preg_replace("[" . preg_quote($tag->name) . "]", $emailTagHandler->$method(), $message);
        }
        $status = explode('-', $order->status->name)[0];
        $subject = "Zmiana statusu - numer oferty: " . $this->orderId . " z: " . str_replace('-', '', $oldStatus?->name ?? 'nie znany')
            . " na: " . str_replace('-', '', $status);

        $mail_to = $order->customer->login;
        $pdfPath = "";

        if (($order->status_id == 3 || $order->status_id == 4) && !$order->sello_id) {
            dispatch_now(new GenerateOrderProformJob($order, true));
            $pdfPath = Storage::disk('local')->path($order->proformStoragePath);
        }

        if ($order->status_id === 3) {
            $orderOfferMessage = Status::find(18)->message;

            $orderOffer = OrderOffer::firstOrNew(['order_id' => $order->id, 'message' => $orderOfferMessage]);
            $orderOffer->message = $orderOfferMessage;
            $orderOffer->save();
        }

        try {
            if (empty($message) || $message === '<p>&nbsp;</p>') {
                return;
            }
//            Mailer::create()
//                ->to($mail_to)
//                ->send(new OrderStatusChanged($subject, $message, $pdfPath));
        } catch (Exception $e) {
            Log::error('Mailer can\'t send email', ['message' => $e->getMessage(), 'path' => $e->getTraceAsString()]);
        }
    }
}
