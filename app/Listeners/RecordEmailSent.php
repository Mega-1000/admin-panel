<?php
namespace App\Listeners;
use App\MailReport;
use Illuminate\Mail\Events\MessageSent;
use \Symfony\Component\Mime\Address;
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
        $receivers = array_map(function (Address $receiver) {
            return $receiver->getAddress();
        }, $event->message->getTo());

        if (!in_array(implode(",", $receivers), ['info@mega1000.pl', '005@mega1000.pl', '002@mega1000.pl'])) {
            MailReport::create([
                'email' => implode(",", $receivers),
                'subject' => $event->message->getSubject(),
                'body' => $event->message->getBody()->bodyToString(),
            ]);
        }
    }
}
