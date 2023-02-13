<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class SendDailyProtocolToDeliveryFirmMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $path;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $path)
    {
        $this->subject = $subject;
        $this->path = $path;
    }

    /**
     * Build the message.
     *
     * @return $this
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
