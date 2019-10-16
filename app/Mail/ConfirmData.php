<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConfirmData extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public $orderId;

    /**
     * @param $subject
     * @param $orderId
     * @param $invoiceId
     */
    public function __construct($subject, $orderId)
    {
        $this->subject = $subject;
        $this->orderId = $orderId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.confirm-customer-data')
            ->subject($this->subject);
    }
}
