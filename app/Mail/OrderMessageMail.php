<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class OrderMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $mailBody;
    public string $pdf;

    /**
     * OrderMessageMail constructor.
     */
    public function __construct(string $subject, string $message, string $pdf = '')
    {
        $this->subject = $subject;
        $this->mailBody = nl2br($message);
        $this->pdf = $pdf ?? '';
    }

    /**
     * Build the message.
     */
    public function content(): Content
    {
        return new Content('emails.order-message');
    }

    public function attachments(): array
    {
        if ($this->pdf !== '') {
            return [
                Attachment::fromPath($this->pdf)->withMime('application/pdf')->as('proforma.pdf')
            ];
        }

        return [];
    }
}

