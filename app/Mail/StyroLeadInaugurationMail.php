<?php

namespace App\Mail;

use App\StyroLeadMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StyroLeadInaugurationMail extends Mailable
{
    use Queueable, SerializesModels;

    public StyroLeadMail $mail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        StyroLeadMail $mail,
    )
    {
        $this->mail = $mail;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Chcesz oszczędzić na styropianie?',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.styro-inauguration',
            with: [
                'mail' => $this->mail,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}
