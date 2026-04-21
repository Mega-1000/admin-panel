<?php

namespace App\Mail;

use App\Entities\ChatAuction;
use App\StyroLeadMail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserHasBeenNotifiedAboutEndOfAuction extends Mailable
{
    use Queueable, SerializesModels;

    public ChatAuction $auction;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ChatAuction $auction) {
        $this->auction = $auction;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Dziękuję za rozmowę, przesyłam jeszcze parę informacji.',
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
            view: 'emails.auction-end-after-call',
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
