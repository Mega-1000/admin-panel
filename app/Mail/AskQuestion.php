<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AskQuestion extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $details;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $emailAddress;

    /**
     * AskQuestion constructor.
     *
     * @param $firstName
     * @param $lastName
     * @param $details
     * @param $phone
     * @param $emailAddress
     */
    public function __construct($firstName, $lastName, $details, $phone, $emailAddress)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->details = $details;
        $this->phone = $phone;
        $this->emailAddress = $emailAddress;
        $this->date = new \DateTime();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.ask-question', [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'details' => $this->details,
            'phone' => $this->phone,
            'date' => $this->date,
        ])
            ->from($this->emailAddress)
            ->subject('Zapytanie od klienta');
    }
}