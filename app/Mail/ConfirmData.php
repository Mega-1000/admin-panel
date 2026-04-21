<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class ConfirmData extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public $orderId;

    /**
     * @param $subject
     * @param $orderId
     * @param $invoiceId
     */
    public function __construct($subject, $orderId)
    {
        $this->subject = $subject;
        $this->orderId = $orderId;
    }

    /**
     * Build the message.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content('emails.confirm-customer-data');
    }
}
