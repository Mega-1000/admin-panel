<?php

namespace App\Jobs;

use App\Entities\Chat;
use App\Facades\Mailer;
use App\Mail\ChatNotInUseNotificationEmail;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckChatsForNotInUse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $chats = Chat::where('is_active', true)->where('information_about_chat_inactiveness_sent', false)->where('created_at', '>', Carbon::create('20.05.2024'))->get();

        foreach ($chats as $chat) {
            $lastMessageSentTime = $chat->messages->orderBy('created_at', 'desc')->first()->created_at;

            if (Carbon::create($lastMessageSentTime)->addDays(2) < now()) {
                Mailer::create()
                    ->to($chat->order->customer->login)
                    ->send(new ChatNotInUseNotificationEmail(
                        $chat
                    ));

                $chat->information_about_chat_inactiveness_sent = true;
                $chat->save();
            }
        }
    }
}
