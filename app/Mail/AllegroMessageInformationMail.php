<?php

namespace App\Mail;

use App\Entities\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class AllegroMessageInformationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param string $messageText
     * @param string $questionsTree
     * @param Customer $customer
     */
    public function __construct(
        protected readonly string   $messageText,
        protected readonly string   $questionsTree,
        protected readonly Customer $customer,
    ) {}

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Informacja o wiadomości allegro',
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
            view: 'emails.allegro_message_information',
            with: [
                'messageText' => $this->messageText,
                'questionsTree' => $this->questionsTree,
                'user' => $this->customer,
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
