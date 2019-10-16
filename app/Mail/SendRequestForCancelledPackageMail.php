<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRequestForCancelledPackageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $package;

    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $package, $url)
    {
        $this->subject = $subject;
        $this->package = $package;
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
