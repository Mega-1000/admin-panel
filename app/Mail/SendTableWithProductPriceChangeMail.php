<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTableWithProductPriceChangeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sendFormWithProducts;

    public $symbol;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $sendFormWithProducts, $symbol)
    {
        $this->subject = $subject;
        $this->sendFormWithProducts = $sendFormWithProducts;
        $this->symbol = $symbol;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.send-products-price-change');
    }
}
