<?php

namespace App\Mail;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class AskQuestion extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var DateTime
     */
    protected DateTime $date;

    public $subject = 'Zapytanie od klienta';

    /**
     * AskQuestion constructor.
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $details
     * @param string $phone
     * @param string $emailAddress
     */
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $details,
        public readonly string $phone,
        public readonly string $emailAddress
    )
    {
        $this->from = $emailAddress;
        $this->date = new DateTime();
    }

    /**
     * Build the message.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content('emails.ask-question');
    }
}
