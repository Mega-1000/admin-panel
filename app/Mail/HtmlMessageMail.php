<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class HtmlMessageMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $messageBody;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message, $subject)
    {
        $this->messageBody = nl2br($message);
        $this->subject = $subject;
    }

    /**
     * Build the message.
     */
    public function content(): Content
    {
        return new Content('emails.html-message');
    }
}
