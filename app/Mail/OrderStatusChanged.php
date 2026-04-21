<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public string $mailBody;
    public string $pdfPath;

    /**
     * OrderStatusChanged constructor.
     */
    public function __construct(string $subject, string $message, ?string $pdfPath = '')
    {
        $this->subject = $subject;
        $this->mailBody = nl2br($message);
        $this->pdfPath = $pdfPath ?? '';
    }

    /**
     * Build the message.
     */
    public function content(): Content
    {
        return new Content(view: 'emails.order-status-changed');
    }

    public function attachments(): array
    {
        return [];
    }


}

