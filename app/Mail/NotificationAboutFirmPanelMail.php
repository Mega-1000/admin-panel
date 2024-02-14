<?php

namespace App\Mail;

use App\Entities\Firm;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationAboutFirmPanelMail extends Mailable
{
    use Queueable, SerializesModels;

    public Firm $firm;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        Firm $firm
    )
    {
        $this->firm = $firm;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Panel firmy w naszym systemie przetargów',
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
            view: 'emails.notifiction-about-firm-panel',
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
