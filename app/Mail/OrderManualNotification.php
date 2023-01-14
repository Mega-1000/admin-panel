<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\MailServiceProvider;

class OrderManualNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;

    public $msgHeader;

    public $msg;

    public $to;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $subject, string $msgHeader, string $msg)
    {
        config([
            'mail.driver'       => 'smtp',
            'mail.host'         => 's104.linuxpl.com',
            'mail.username'     => 'awizacje@ephpolska.pl',
            'mail.password'     => '1!Qaa2@Wss',
            'mail.port'         => 587,
            'mail.encryption'   => 'tls',
            'mail.from.address' => 'awizacje@ephpolska.pl',
            'mail.from.name'    => 'Awizacje',
        ]);
        (new MailServiceProvider(app()))->register();

        $this->subject = $subject;
        $this->msgHeader = $msgHeader;
        $this->msg = $msg;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.order-manual-notification');
    }
}
