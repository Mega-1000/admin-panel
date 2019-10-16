<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LabelAdd extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public $mailBody;

    /**
     * @param $subject
     * @param $message
     */
    public function __construct($subject, $message)
    {
        $this->subject = $subject;
        $this->mailBody = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.label-add')
            ->subject($this->subject);
    }
}
