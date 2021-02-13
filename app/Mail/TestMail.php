<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use SerializesModels;

    public function build()
    {
        return $this->view('emails.test')
            ->subject('Testowy emamil');
    }
}
