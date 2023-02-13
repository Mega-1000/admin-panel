<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class ShipmentDateInOrderChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $data;

    /**
     * ShipmentDateInOrderChangedMail constructor.
     * @param $subject
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->subject = "Zmiana daty rozpoczęcia nadawania przesyłki dla zamówienia " . $data['orderId'];
    }


    /**
     * Build the message.
     */
    public function content(): Content
    {
        return new Content('emails.shipment-date-in-order-changed');
    }
}
