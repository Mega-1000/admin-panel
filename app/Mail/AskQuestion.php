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
    protected $date;

    public $subject = 'Zapytanie od klienta';

    /**
     * AskQuestion constructor.
     *
     * @param $firstName
     * @param $lastName
     * @param $details
     * @param $phone
     * @param $emailAddress
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
     * @return $this
     */
    public function content(): Content
    {
        return new Content('emails.ask-question');
    }
}
