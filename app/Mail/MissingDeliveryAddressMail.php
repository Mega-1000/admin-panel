<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class MissingDeliveryAddressMail extends Mailable
{
    use Queueable, SerializesModels;

    public $formLink;

    /**
     * OrderStatusChangedToDispatchMail constructor.
     * @param $subject
     * @param $formLink
     */
    public function __construct($subject, $formLink)
    {
        $this->formLink = $formLink;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function content(): Content
    {
        return new Content('emails.missing-delivery-address');
    }
}
