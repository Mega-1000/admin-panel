<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class RequestNewPricesMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $mes;

    /**
     * OrderStatusChanged constructor.
     */
    public function __construct(string $mes)
    {
        $this->subject = 'Prośba o wysłanie cenników';
        $this->mes = $mes;
    }

    /**
     * Build the message.
     */
    public function content(): Content
    {
        return new Content('emails.requestNewPricesMail');
    }
}
