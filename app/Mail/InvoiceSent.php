<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceSent extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public $mailBody;

    public $attachment;

    /**
     * @param $subject
     * @param $message
     * @param $attachment
     */
    public function __construct($subject, $message, $attachment)
    {
        $this->subject = $subject;
        $this->mailBody = $message;
        $this->attachment = $attachment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.invoice-sent')
	        ->subject($this->subject)
	        ->attach('/home/mega1000/domains/mega1000.pro-linuxpl.com/public_html/subiekt/invoices/' . $this->attachment);
        
        return $this;
    }
}
