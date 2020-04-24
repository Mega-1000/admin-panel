<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class WarehousePaymentAccept extends Mailable
{
    use Queueable, SerializesModels;

    public $orderId;

    public $amount;

    public $invoice;

    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($orderId, $amount, $invoice, $token)
    {
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->invoice = $invoice;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.warehouse-accept-payment')
            ->subject('Prośba o akceptację zaksięgowanej kwoty dla zlecenia ' . $this->orderId)
            ->attach(Storage::disk('local')->get('invoices/' . $this->invoice));
    }
}
