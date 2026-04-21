<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class SendLog extends Mailable
{
    use Queueable, SerializesModels;

    public $date;

    public $logMessage;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $logMessage)
    {
        $this->subject = $subject;
        $this->date = date("Y-m-d h:i:s");
        $this->logMessage = $logMessage;
    }

    /**
     * Build the message.
     */
    public function content(): Content
    {
        return new Content('emails.send-log');
    }
}
