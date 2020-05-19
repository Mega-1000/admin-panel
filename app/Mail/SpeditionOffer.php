<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SpeditionOffer extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $order)
    {
        $this->subject = $subject;
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.spedition-offer-summary');
    }
}
