<?php

namespace App\Mail;

use App\Entities\ContactApproach;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CallApproachHasBeenMadeReferee extends Mailable
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
            subject: 'Wykonaliśmy telefon na numer polecony przez ciebie!',
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
            view: 'approaches.call-made-referee',
            with: [
                'approach' => $this->approach,
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
