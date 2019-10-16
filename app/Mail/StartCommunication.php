<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.start-communication');
    }
}
