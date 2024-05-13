<?php

namespace App\Mail;

use App\Entities\OrderPaymentConfirmation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPaymentConfirmationAttachedMail extends Mailable
{
    use Queueable, SerializesModels;

    public OrderPaymentConfirmation $confirmation;
    public bool $isProd;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(OrderPaymentConfirmation $confirmation, bool $isProd)
    {
        $this->confirmation = $confirmation;
        $this->isProd = $isProd;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isProd ? 'Przypomnienie o potwierdzeniu zatwierdzenia płatności' : 'Prośba o potwierdzenie zatwierdzenia płatności',
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
            view: 'emails.payment-confirmation',
            with: ['confirmation' => $this->confirmation],
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
