<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class OrderStatusChangedToDispatchMail extends Mailable
{
    use Queueable, SerializesModels;

    public $formLink;

    public $sendFormInvoice;

    public $order;

    public $self;

    public $path;

    public $packageNumber;

    public $pathSecond;

    /**
     * OrderStatusChangedToDispatchMail constructor.
     * @param $subject
     * @param $formLink
     * @param $sendFormInvoice
     * @param $order
     * @param $self
     */
    public function __construct($subject, $formLink, $sendFormInvoice, $order, $self = null, $path = null, $packageNumber = null, $pathSecond = null)
    {
        ini_set('max_execution_time', 60);
        $this->formLink = $formLink;
        $this->sendFormInvoice = $sendFormInvoice;
        $this->subject = $subject;
        $this->order = $order;
        $this->self = $self;
        $this->path = $path;
        $this->packageNumber = $packageNumber;
        $this->pathSecond = $pathSecond;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::notice('W buildzie', ['line' => __LINE__, 'file' => __FILE__]);
        if($this->self == true) {
            return $this->view('emails.order-status-changed-to-dispatch-self');
        } else {
            if($this->path == null) {
                return $this->view('emails.order-status-changed-to-dispatch');
            } else {
                return $this->view('emails.reminder-order-status-changed-to-dispatch')->attach($this->path)->attach($this->pathSecond);
            }
        }

    }
}
