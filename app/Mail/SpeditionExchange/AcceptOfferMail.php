<?php

namespace App\Mail\SpeditionExchange;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class AcceptOfferMail extends Mailable
{
    use Queueable, SerializesModels;

    public $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $link)
    {
        $this->subject = $subject;
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     */
    public function content(): Content
    {
        return new Content(view: 'emails.spedition-exchange.accept-offer');
    }
}
