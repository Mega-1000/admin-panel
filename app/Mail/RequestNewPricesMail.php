<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class RequestNewPricesMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $message;

    /**
     * OrderStatusChanged constructor.
     */
    public function __construct(string $message)
    {
        $this->subject = 'Prośba o wysłanie cenników';
        $this->message = $message;
    }

    /**
     * Build the message.
     */
    public function content(): Content
    {
        return new Content('emails.requestNewPricesMail');
    }
}
