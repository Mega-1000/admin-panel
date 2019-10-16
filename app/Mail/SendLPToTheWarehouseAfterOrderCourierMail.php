<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendLPToTheWarehouseAfterOrderCourierMail extends Mailable
{
    use Queueable, SerializesModels;

    public $path;

    public $packageId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $path, $packageId)
    {
        $this->subject = $subject;
        $this->path = $path;
        $this->packageId = $packageId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.send-protocol-to-delivery-firm')->attach($this->path);
    }
}
