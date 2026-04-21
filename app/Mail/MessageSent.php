<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class MessageSent extends Mailable
{
    use Queueable, SerializesModels;

    public $date;

    public $type;

    public $typeText;

    public $frontId;

    public $orderId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($date, $type, $typeText, $frontId, $orderId)
    {
        $this->date = $date;
        $this->type = $type;
        $this->typeText = $typeText;
        $this->frontId = $frontId;
        $this->orderId = $orderId;
        // TODO Zmienić na config
        $this->subject = 'Nowa wiadomość od www.' . config('app.domain_name');
    }

    /**
     * Build the message.
     */
    public function build(): Content
    {
        return new Content('emails.message-to-client');
    }
}
