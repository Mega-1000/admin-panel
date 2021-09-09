<?php

namespace App\Jobs;

use App\Helpers\EmailTagHandlerHelper;
use App\Jobs\Orders\GenerateOrderProformJob;
use App\Mail\OrderStatusChanged;
use App\Repositories\StatusRepository;
use App\Repositories\TagRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;


/**
 * Class OrderProformSendMailJob
 * @package App\Jobs
 */
class OrderProformSendMailJob extends Job implements ShouldQueue
{
	use Queueable, SerializesModels;
    /**
     * @var
     */
    protected $order;

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
     * @param $order
     * @param $message
     * @param $oldStatus
     */
    public function __construct($order, $message = null)
    {
        $this->order = $order;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(EmailTagHandlerHelper $emailTagHandler, TagRepository $tagRepository, StatusRepository $statusRepository)
    {
        $tags = $tagRepository->all();

        if ($this->message !== null) {
            $message = $this->message;
        } else {
            $message = $this->order->status->message;
        }

        $emailTagHandler->setOrder($this->order);

        foreach ($tags as $tag) {
            $method = $tag->handler;
            $message = preg_replace("[" . preg_quote($tag->name) . "]", $emailTagHandler->$method(), $message);
        }
        
        $subject = "Numer oferty: " . $this->order->id . ", status: " . $this->order->status->name . ' oraz proforma';

        if (!$this->order->proforma_filename || !Storage::disk('local')->exists($this->order->proformStoragePath)) {
	        dispatch_now(new GenerateOrderProformJob($this->order));
        }
        
        $mail_to = $this->order->customer->login;
        $pdf = Storage::disk('local')->get($this->order->proformStoragePath);
        
        try {
            \Mailer::create()
                ->to($mail_to)
                ->send(new OrderStatusChanged($subject, $message, $pdf));
        } catch (\Exception $e) {
            \Log::error('Mailer can\'t send email', ['message' => $e->getMessage(), 'path' => $e->getTraceAsString()]);
        }
    }
}
