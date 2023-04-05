<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class AllegroNewOrderEmail extends Mailable
{
    use SerializesModels;

    public $subject = 'Informacje o Twoim zakupie';
    
    public function content(): Content
    {
        return new Content('emails.allegro-new-order');
    }
}
