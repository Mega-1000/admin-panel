<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CheckDeliveryAddressMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailBody;
    public $pdf;

    /**
     * CheckDeliveryAddressMail constructor.
     * @param $subject
     * @param $message
     * @param $pdf
     */
    public function __construct($subject, $message, $pdf = false)
    {
        $this->subject = $subject;
        $this->mailBody = nl2br($message);
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        if ($this->pdf) {
            return $this->view('emails.check-delivery-address')
                ->attachData($this->pdf, 'attachment.pdf', [
                    'mime' => 'application/pdf',
                ]);
        } else {
            return $this->view('emails.check-delivery-address');
        }
    }
}
