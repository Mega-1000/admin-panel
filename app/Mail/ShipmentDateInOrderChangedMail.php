<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.shipment-date-in-order-changed');
    }
}
