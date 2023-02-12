<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
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
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        return $this->view('emails.message-to-warehouse')
            ->from('info@' . env('DOMAIN_NAME'))
            ->subject('Nowa wiadomość od www.' . env('DOMAIN_NAME'));
    }
}
