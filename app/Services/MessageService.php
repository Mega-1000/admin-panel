<?php

namespace App\Services;

use App\DTO\CreateTWSOOrdersDTO;
use App\DTO\Messages\CreateMessageDTO;
use App\Exceptions\ChatException;
use App\Helpers\MessagesHelper;

readonly class MessageService
{
    public function __construct(
        public MessagesHelper $messagesHelper
    ) {}

    /**
     * @throws \App\Helpers\Exceptions\ChatException
     */
    public function addMessage(CreateMessageDTO $data): string
    {
        $helper = new MessagesHelper($data->token);
        $chat = $helper->getChat();

        if (!$chat) {
            $helper->createNewChat();
        }

        if (!$helper->canUserSendMessage()) {
            throw new \App\Helpers\Exceptions\ChatException('User not allowed to send message');
        }

        $file = $data->file ?? null;
        $message = $helper->addMessage($data->message, $data->area, $file);
        $helper->setLastRead();

        return $message;
    }
}
