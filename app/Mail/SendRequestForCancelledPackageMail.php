<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

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
     */
    public function content(): Content
    {
        return new Content('emails.send-request-for-cancelled-package');
    }
}
