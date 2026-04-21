<?php

namespace App\Mail;

use App\Entities\ContactApproach;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CallApproachHasBeenMadeProspect extends Mailable
{
    use Queueable, SerializesModels;

    public ContactApproach $approach;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ContactApproach $approach)
    {
        $this->approach = $approach;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Potwierdzenie rozmowy z EPH Polska w sprawie styropianu',
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
            view: 'approaches.call-made-prospect',
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
