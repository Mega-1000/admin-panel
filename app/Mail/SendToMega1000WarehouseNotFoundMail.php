<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class SendToMega1000WarehouseNotFoundMail extends Mailable
{
    use Queueable, SerializesModels;

    public $supplier;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $supplier)
    {
        $this->subject = $subject;
        $this->supplier = $supplier;
    }

    /**
     * Build the message.
     */
    public function content(): Content
    {
        return new Content('emails.send-to-mega1000-warehouse-not-found');
    }
}
