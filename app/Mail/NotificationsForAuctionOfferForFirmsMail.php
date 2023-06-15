<?php

namespace App\Mail;

use App\Entities\ChatAuctionOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use function Clue\StreamFilter\fun;

class NotificationsForAuctionOfferForFirmsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        private readonly ChatAuctionOffer $chatAuctionOffer,
        private readonly string $email,
    ) {}

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Powiadomienie o nowej ofercie w aukcji',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {;
        return new Content(
            view: 'emails.notification-for-auction-offer-for-firms-mail',
            with: [
                'chatAuctionOffer' => $this->chatAuctionOffer,
                'chatAuctionFirm' => $this->chatAuctionOffer->whereHas('firm', function ($query) {
                    $query->where('email', $this->email);
                })->first(),
            ],
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
