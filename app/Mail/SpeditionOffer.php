<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class SpeditionOffer extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $order)
    {
        $this->subject = $subject;
        $this->order = $order;
    }

    /**
     * Build the message.
     */
    public function build(): Content
    {
        return new Content('emails.spedition-offer-summary');
    }
}
