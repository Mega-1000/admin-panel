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
        dd($event->data);
        MailReport::create([
            'email' => key($event->data->getTo()),
            'subject' => $event->message->getSubject(),
            'body' => $event->message->getBody(),
        ]);
    }
}
