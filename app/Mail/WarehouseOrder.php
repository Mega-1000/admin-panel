<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WarehouseOrder extends Mailable
{
    use Queueable, SerializesModels;

    public $mailBody;

    /**
     * OrderStatusChanged constructor.
     * @param $subject
     * @param $message
     */
    public function __construct($subject, $message)
    {
        $this->subject = $subject;
        $this->mailBody = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.warehouseOrder');
    }
}
