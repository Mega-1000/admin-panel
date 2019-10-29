<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRequestForCancelledPackageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sendingNumber;

    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $sendingNumber, $url)
    {
        $this->subject = $subject;
        $this->sendingNumber = $sendingNumber;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.send-request-for-cancelled-package');
    }
}
