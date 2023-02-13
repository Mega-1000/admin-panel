<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class SendLPToTheWarehouseAfterOrderCourierMail extends Mailable
{
    use Queueable, SerializesModels;

    public $path;

    public $packageId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $path, $packageId)
    {
        $this->subject = $subject;
        $this->path = $path;
        $this->packageId = $packageId;
    }

    /**
     * Build the message.
     */
    public function content(): Content
    {
        return new Content('emails.send-protocol-to-delivery-firm');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->path),
        ];
    }
}
