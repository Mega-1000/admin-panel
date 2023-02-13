<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class SelfMessageSent extends Mailable
{
    use Queueable, SerializesModels;

    public $date;

    public $type;

    public $typeText;

    public $warehouseId;

    public $orderId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($date, $type, $typeText, $warehouseId, $orderId)
    {
        $this->date = $date;
        $this->type = $type;
        $this->typeText = $typeText;
        $this->warehouseId = $warehouseId;
        $this->orderId = $orderId;
        // TODO Zamienić na config
        $this->subject = 'Nowa wiadomość od www.' . env('DOMAIN_NAME');
    }

    /**
     * Build the message.
     */
    public function content(): Content
    {
        return new Content('emails.message-to-warehouse');
    }
}
