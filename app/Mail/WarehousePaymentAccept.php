<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class WarehousePaymentAccept extends Mailable
{
    use Queueable, SerializesModels;

    public $orderId;

    public $amount;

    public $invoice;

    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($orderId, $amount, $invoice, $url)
    {
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->invoice = $invoice;
        $this->url = $url;
        $this->subject = 'Prośba o akceptację zaksięgowanej kwoty dla zlecenia ' . $this->orderId;
    }

    /**
     * Build the message.
     */
    public function content(): Content
    {
        return new Content('emails.warehouse-accept-payment');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath(Storage::disk('local')->get('invoices/' . $this->invoice)),
        ];
    }
}
