<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class SendRequestToUpdateDataMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $url)
    {
        $this->subject = $subject;
        $this->url = $url;
    }

    /**
     * Build the message.
     */
    public function content(): Content
    {
        return new Content('emails.send-request-to-update-firm-data');
    }
}
