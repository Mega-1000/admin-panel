<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\MailServiceProvider;

class OrderManualNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public $msgHeader;

    public $msg;

    public $to;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $subject, string $msgHeader, string $msg)
    {
        $this->subject = $subject;
        $this->msgHeader = $msgHeader;
        $this->msg = $msg;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.order-manual-notification');
    }
}
