<?php

namespace App\Jobs;

use App\Entities\Order;
use App\Helpers\EmailTagHandlerHelper;
use App\Mail\OrderStatusChanged;
use App\Repositories\OrderRepository;
use App\Repositories\StatusRepository;
use App\Repositories\TagRepository;
use App\Services\AllegroDisputeService;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


/**
 * Class OrderStatusChangedNotificationJob
 * @package App\Jobs
 */
class OrderStatusChangedNotificationJob extends Job implements ShouldQueue
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
            $proformDate = Carbon::now()->format('m/Y');
            $date = Carbon::now()->toDateString();
            if ($order->proforma_filename) {
                Storage::disk('local')->delete('public/proforma/' . $order->proforma_filename);
            }
            $order->proforma_filename = Str::random(40) . '.pdf';
            $order->save();
            $pdf = PDF::loadView('pdf.proform', compact('date', 'proformDate', 'order'))->output();
            Storage::disk('local')->put('public/proforma/' . $order->proforma_filename, $pdf);

            $mail_to = $order->customer->login;
            
            try {
                if ($selloTransaction = $order->selloTransaction) {
	                if ($allegroOrder = $selloTransaction->allegroOrder) {
		                $mail_to = $allegroOrder->buyer_email;
	                } else {
		                \Log::info("Order: {$order->id}. Allegro order not found.");
	                }
                } else {
	                \Log::info("Order: {$order->id}. Sello transaction not found.");
                }
	
	            \Log::info("Order: {$order->id}. Send to: {$mail_to}");
                
                /*
                \Mailer::create()
                    ->to($mail_to)
                    ->send(new OrderStatusChanged($subject, $message, $pdf));
                */
            } catch (\Exception $e) {
                \Log::error('Mailer can\'t send email', ['message' => $e->getMessage(), 'path' => $e->getTraceAsString()]);
            }
        }
    }
}
