<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

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
    public function content(): Content
    {
        return new Content('emails.invoice-sent');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath('/home/mega1000/domains/mega1000.pro-linuxpl.com/public_html/subiekt/invoices/' . $this->attachment),
        ];
    }
}
