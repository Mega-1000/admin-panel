<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class RequestNewPricesMail extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * OrderStatusChanged constructor.
     */
    public function __construct()
    {
        $this->subject = 'Prośba o wysłanie cenników';
    }

    /**
     * Build the message.
     */
    public function content(): Content
    {
        return new Content('emails.requestNewPricesMail');
    }
}
