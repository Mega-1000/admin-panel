<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

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
     */
    public function content(): Content
    {
        return new Content('emails.send-products-price-change');
    }
}
