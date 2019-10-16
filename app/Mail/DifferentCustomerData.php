<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DifferentCustomerData extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public $orderId;

    public $invoiceId;

    /**
     * @param $subject
     * @param $orderId
     * @param $invoiceId
     */
    public function __construct($subject, $orderId, $invoiceId)
    {
        $this->subject = $subject;
        $this->orderId = $orderId;
        $this->invoiceId = $invoiceId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.different-customer-data')
            ->subject($this->subject);
    }
}
