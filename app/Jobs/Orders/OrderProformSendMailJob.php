<?php

namespace App\Jobs;

use App\Facades\Mailer;
use App\Helpers\EmailTagHandlerHelper;
use App\Jobs\Orders\GenerateOrderProformJob;
use App\Mail\OrderMessageMail;
use App\Repositories\TagRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
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
     * Requires to pass id of Order that's status changed to ::dispatch()
     *
     * @param $order
     * @param null $message
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
    public function handle(EmailTagHandlerHelper $emailTagHandler, TagRepository $tagRepository)
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

        dispatch_now(new GenerateOrderProformJob($this->order));

        $mail_to = $this->order->customer->login;
        $pdf = Storage::disk('local')->path($this->order->proformStoragePath);

        try {
            Mailer::create()
                ->to($mail_to)
                ->send(new OrderMessageMail($subject, $message, $pdf));
        } catch (Exception $e) {
            Log::error('Mailer can\'t send email', ['message' => $e->getMessage(), 'path' => $e->getTraceAsString()]);
        }
    }
}
