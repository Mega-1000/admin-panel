<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CheckDeliveryAddressMail extends Mailable
{
	use Queueable, SerializesModels;
	
	public $formLink;
	
	/**
	 * OrderStatusChangedToDispatchMail constructor.
	 * @param $subject
	 * @param $formLink
	 */
	public function __construct($subject, $formLink)
	{
		$this->formLink = $formLink;
		$this->subject = $subject;
	}
	
	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->view('emails.check-delivery-address');
	}
}
