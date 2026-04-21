<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

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
     */
    public function content(): Content
    {
        return new Content('emails.warehouseOrder');
    }
}
