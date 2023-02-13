<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class DifferentCustomerData extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public $orderId;

    public $invoiceId;

    /**
     * @param $subject
     * @param $orderId
     * @param $invoiceId
     */
    public function __construct($subject, $orderId, $invoiceId)
    {
        $this->subject = $subject;
        $this->orderId = $orderId;
        $this->invoiceId = $invoiceId;
    }

    /**
     * Build the message.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content('emails.different-customer-data');
    }
}
