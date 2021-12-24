<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GroupOrders extends Mailable
{
    use SerializesModels;
    
    public function build()
    {
        return $this->view('emails.group-orders')
            ->subject('Informacje o Twoim zakupie');
    }
}
