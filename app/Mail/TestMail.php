<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use SerializesModels;

    public $subject = 'Testowy email';

    public function content(): Content
    {
        return new Content('emails.test');
    }
}
