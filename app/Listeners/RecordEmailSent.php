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
            'email' => $event->data['email'],
            'subject' => $event->data['title'],
            'body' => $event->data['message'],
        ]);
    }
}
