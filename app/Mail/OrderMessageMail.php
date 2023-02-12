<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $mailBody;
    public bool $pdf;

    /**
     * OrderMessageMail constructor.
     */
    public function __construct(string $subject, string $message, bool $pdf = false)
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
            return $this->view('emails.order-message')
                ->attachData($this->pdf, 'proforma.pdf', [
                    'mime' => 'application/pdf',
                ]);
        } else {
            return $this->view('emails.order-message');
        }
    }
}

