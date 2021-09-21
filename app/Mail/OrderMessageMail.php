<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailBody;
    public $pdf;

    /**
     * OrderMessageMail constructor.
     * @param $subject
     * @param $message
     * @param $pdf
     */
    public function __construct($subject, $message, $pdf = false)
    {
        $this->subject = $subject;
        $this->mailBody = $message;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->pdf != false) {
            return $this->view('emails.order-message')
                ->attachData($this->pdf, 'proforma.pdf', [
                    'mime' => 'application/pdf',
                ]);
        } else {
            return $this->view('emails.order-message');
        }
    }
}

