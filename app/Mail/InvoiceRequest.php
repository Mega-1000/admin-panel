<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class InvoiceRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $orderId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
        $this->subject = 'Prośba o wprowadzenie faktury dla zlecenia ' . $this->orderId;
    }

    /**
     * Build the message.
     *
     */
    public function content(): Content
    {
        return new Content('emails.warehouse-invoice-request');
    }
}
