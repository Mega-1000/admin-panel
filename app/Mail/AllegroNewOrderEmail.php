<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AllegroNewOrderEmail extends Mailable
{
    use SerializesModels;

    public function build()
    {
        return $this->view('emails.allegro-new-order.blade.php')
            ->subject('Informacje o Twoim zakupie');
    }
}
