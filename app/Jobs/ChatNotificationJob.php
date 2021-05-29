<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Helpers\MessagesHelper;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ChatNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    private $chatId;

    private $senderEmail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chatId, $senderEmail = false)
    {
        $this->chatId = $chatId;
        $this->senderEmail = $senderEmail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $chat = \App\Entities\Chat
            ::with(['chatUsers', 'messages'])->find($this->chatId);

        foreach ($chat->chatUsers as $chatUser) {
            /*if (!MessagesHelper::hasNewMessageStatic($chat, $chatUser, true)) {
                continue;
            }*/
            $userObject = $chatUser->user ?: $chatUser->employee ?: $chatUser->customer ?: $chatUser->user ?:false;
            if (!$userObject) {
                continue;
            }
            $this->sendMail($chat, $chatUser, $userObject);
        }
    }

    private function sendMail($chat, $chatUser, $userObject)
    {
        $helper = new MessagesHelper();
        $helper->chatId = $chat->id;
        $helper->currentUserType = $chatUser->user ? MessagesHelper::TYPE_USER : ($chatUser->employee ? MessagesHelper::TYPE_EMPLOYEE : MessagesHelper::TYPE_CUSTOMER);
        $helper->currentUserId = $userObject->id;
        try {
            $email = $userObject->email ?? $userObject->login;
            if ($email == $this->senderEmail) {
                return;
            }
            self::sendNewMessageEmail($email, $helper);
            $chatUser->last_notification_time = now();
            $chatUser->save();
        } catch (\Exception $e) {
            \Log::error('ChatNotification Exception: ' . $e->getMessage() . ', Class: ' . $e->getFile() . ', Line: ' . $e->getLine());
        }
    }

    public static function sendNewMessageEmail($email, MessagesHelper $helper): void
    {
        \App\Helpers\Helper::sendEmail(
            $email,
            'chat-notification',
            'Nowa wiadomość w ' . env('APP_NAME'),
            [
                'url' => route('chat.show', ['token' => $helper->encrypt()]),
                'title' => $helper->getTitle(false)
            ]
        );
    }
}
