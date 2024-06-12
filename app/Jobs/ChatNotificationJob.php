<?php

namespace App\Jobs;

use App\Entities\Message;
use Exception;
use App\Entities\Chat;
use App\Helpers\Helper;
use Illuminate\Bus\Queueable;
use App\Helpers\MessagesHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ChatNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private int $chatId,
        private ?string $senderEmail = null,
        private int $currentChatUserId = 0,
        public boolean $isMainArea = false
    ) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $chat = Chat::with(['chatUsers', 'messages'])->find($this->chatId);
        $userVisibilityString = '';

        if (!$this->isMainArea) {
            return;
        }


        foreach ($chat->chatUsers as $chatUser) {
            if (
                $chatUser->id == $this->currentChatUserId
            ) continue;

            $userType = null;
            $userObject = null;
            if ($chatUser->user) {
                $userObject = $chatUser->user;
                $userType = 'u'; // User
            } elseif ($chatUser->employee) {
                $userObject = $chatUser->employee;
                $userType = 'e'; // Employee
            } elseif ($chatUser->customer) {
                $userObject = $chatUser->customer;
                $userType = 'c'; // Customer
            } else {
                $userObject = false; // None of the properties are set
            }


            if (!$userObject) {
                continue;
            }

            $this->sendMail($chat, $chatUser, $userObject);

            $userVisibilityString .= ' ' . $userType;
        }

        $lastMessage = Message::where('chat_id', $chat->id)->orderBy('created_at', 'desc')->first();
        $lastMessage->users_visibility  = $userVisibilityString;
        $lastMessage->save();
    }

    private function sendMail($chat, $chatUser, $userObject)
    {
        $helper = new MessagesHelper();
        $helper->chatId = $chat->id;
        $helper->currentUserType = $chatUser->user ? MessagesHelper::TYPE_USER : ($chatUser->employee ? MessagesHelper::TYPE_EMPLOYEE : MessagesHelper::TYPE_CUSTOMER);
        $helper->currentUserId = $userObject->id;

        try {
            $email = $userObject->email ?? $userObject->login;

            self::sendNewMessageEmail($email, $helper);
            $chatUser->last_notification_time = now();
            $chatUser->save();
        } catch (Exception $e) {
            Log::error('ChatNotification Exception: ' . $e->getMessage() . ', Class: ' . $e->getFile() . ', Line: ' . $e->getLine());
        }
    }

    public static function sendNewMessageEmail($email, MessagesHelper $helper): void
    {
        Helper::sendEmail(
            $email,
            'chat-notification',
            'Nowa wiadomość w ' . config('app.name'),
            [
                'url' => route('chat.show', ['token' => $helper->encrypt()]),
                'title' => $helper->getTitle(false)
            ]
        );

//        Helper::sendEmail(
//            'antoniwoj@o2.pl',
//            'chat-notification',
//            'Nowa wiadomość w ' . config('app.name') . 'na email ' . $email,
//            [
//                'url' => route('chat.show', ['token' => $helper->encrypt()]),
//                'title' => $helper->getTitle(false)
//            ]
//        );
    }
}
