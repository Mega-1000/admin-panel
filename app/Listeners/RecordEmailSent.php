<?php

namespace App\Listeners;

use App\MailReport;
use Illuminate\Mail\Events\MessageSent;

class RecordEmailSent
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MessageSent  $event
     * @return void
     */
    public function handle(MessageSent $event): void
    {
        MailReport::create([
            'email' => key($event->message->getTo()),
            'subject' => $event->message->getSubject(),
            'body' => $event->message->getBody(),
        ]);
    }
}
