<?php

namespace App\Mail;

use App\Entities\Firm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuctionNotResponded extends Mailable
{
    use Queueable, SerializesModels;

    public Firm $firm;
    public int $amountOfAuctions;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        Firm $firm,
        int $amountOfAuctions
    ) {
        $this->firm = $firm;
        $this->amountOfAuctions = $amountOfAuctions;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Nie odpowiedziałeś na ' . $this->amountOfAuctions . ' zapytań ofertowych',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.auction-not-responded',
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
