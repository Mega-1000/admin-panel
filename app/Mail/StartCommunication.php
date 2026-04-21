<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class StartCommunication extends Mailable
{
    use Queueable, SerializesModels;

    public $orderId;

    /**
     * OrderStatusChanged constructor.
     * @param $orderId
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
        $this->subject = "Komunikacja do zlecenia: " . $orderId;
    }

    /**
     * Build the message.
     */
    public function build(): Content
    {
        return new Content('emails.start-communication');
    }
}
