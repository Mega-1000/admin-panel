<?php

namespace App\Mail;

use App\Entities\ChatAuction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuctionCreationConfirmation extends Mailable
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
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Przetarg o numerze : ' . $this->auction->chat->order->id . ' na styropian został stworzony na twoim koncie!',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.auction-cofirmation',
            with: [
                'auction' => $this->auction
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
