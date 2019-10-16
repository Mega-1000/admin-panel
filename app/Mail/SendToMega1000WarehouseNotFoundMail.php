<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.send-to-mega1000-warehouse-not-found');
    }
}
