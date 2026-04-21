<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class CheckDeliveryAddressMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $mailBody;
    public string $pdf;

    /**
     * CheckDeliveryAddressMail constructor.
     * @param $subject
     * @param $message
     * @param $pdf
     */
    public function __construct($subject, $message, $pdfPath = false)
    {
        $this->subject = $subject;
        $this->mailBody = nl2br($message);
        $this->pdf = $pdfPath ?? '';
    }

    /**
     * Build the message.
     */
    public function content(): Content
    {
        return new Content('emails.check-delivery-address');
    }

    public function attachments(): array
    {
        if ($this->pdf !== '') {
            return [
                Attachment::fromPath($this->pdf)
                    ->as('attachment.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
