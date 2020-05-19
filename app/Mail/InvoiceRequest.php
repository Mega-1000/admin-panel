<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

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
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.warehouse-invoice-request')
            ->subject('Prośba o wprowadzenie faktury dla zlecenia ' . $this->orderId);
    }
}
