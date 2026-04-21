<?php

namespace App\Jobs;

use App\Entities\Chat;
use App\Entities\Order;
use App\Facades\Mailer;
use App\Helpers\MessagesHelper;
use App\Helpers\SMSHelper;
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
    public function handle(): void
    {
        $chats = Chat::whereHas('messages', function ($q) {$q->whereIn('id', function($subquery) {$subquery->selectRaw('MAX(id)')->from('messages')->groupBy('chat_id');})->where('sent_sms', false);})->where('created_at', '>', Carbon::create('2024', '05', '20'))->get();

        foreach ($chats as $chat) {
            $lasMessage = $chat->messages()->orderBy('created_at', 'desc')->first();
            $lastMessageSentTime = $lasMessage->created_at;

            $messagesHelper = new MessagesHelper();
            $messagesHelper->chatId = $chat->id;
            $token = $messagesHelper->getChatToken($chat->order->id, $chat->order->customer?->id, 'c');

            // if last message more than 4 hours ago
            if ($token && $lasMessage && !empty($lasMessage->chatUser->user_id) && Carbon::create($lastMessageSentTime)->addHours(4) < now()) {
                SMSHelper::sendSms(
                    576205389,
                    "EPH Polska",
                    "Dzien dobry, informujemy ze na panelu klienta w EPH Polska masz nie odczytaną wiadomosc na chacie. Kliknij tutaj aby ją wyswietlic i odpisac:
https://admin.mega1000.pl/chat/$token",
                );

                $lasMessage->sent_sms = true;
                $lasMessage->save();
            }
        }

        $chats = Chat::where('information_about_chat_inactiveness_sent', false)->where('created_at', '>', Carbon::create('2024', '05', '20'))->get();

        foreach ($chats as $chat) {
            $lastMessageSentTime = $chat->messages()->orderBy('created_at', 'desc')->first()->created_at;

            if (!empty($lastMessageSentTime) && Carbon::create($lastMessageSentTime)->addDays(2) < now()) {
                Mailer::create()
                    ->to($chat->order->customer->login)
                    ->send(new ChatNotInUseNotificationEmail($chat));

                $chat->information_about_chat_inactiveness_sent = true;
                $chat->save();
            }

            if (Carbon::create($lastMessageSentTime)->addDays(4) < now() && $chat->order->labels->contains(56)) {
                $chat->order->labels()->detach(56);
            }
        }
    }
}
