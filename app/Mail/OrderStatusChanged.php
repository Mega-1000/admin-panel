<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public string $mailBody;
    public bool $pdf;

    /**
     * OrderStatusChanged constructor.
     */
    public function __construct(string $subject, string $message, bool $pdf = false)
    {
        $this->subject = $subject;
        $this->mailBody = nl2br($message);
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        if ($this->pdf) {
            return $this->view('emails.order-status-changed')
                ->attachData($this->pdf, 'proforma.pdf', [
                    'mime' => 'application/pdf',
                ]);
        }

        return $this->view('emails.order-status-changed');
    }
}

