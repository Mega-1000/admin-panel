<?php

namespace App\Mail;

use App\Entities\ChatAuction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuctionFinishedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public ChatAuction $auction;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ChatAuction $auction)
    {
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
            subject: 'Przetarg na twoim koncie został zakończony',
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
            view: 'auction-finished',
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
