<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HtmlMessageMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
	
	public $messageBody;
	public $subject;
	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct($message, $subject)
	{
		$this->messageBody = nl2br($message);
		$this->subject = $subject;
	}
	
	/**
	 * Build the message.
	 *
	 * @return $this
	 */
    public function build()
    {
        return $this->view('emails.html-message')
            ->subject($this->subject);
    }
}
