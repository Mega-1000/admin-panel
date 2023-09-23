<?php

namespace App\Services;

use App\DTO\Messages\CreateMessageDTO;
use App\Entities\ChatUser;
use App\Entities\Customer;
use App\Entities\Employee;
use App\Entities\Message;
use App\Helpers\Exceptions\ChatException;
use App\Helpers\MessagesHelper;
use Illuminate\Http\Request;

readonly class MessageService
{
    public function __construct(
        public MessagesHelper $messagesHelper
    ) {}


    /**
     * @param $chat
     * @param Request $request
     * @param $user
     */
    public static function createNewCustomerOrEmployee($chat, Request $request, $user): void
    {
        $chatUser = new ChatUser();
        $chatUser->chat()->associate($chat);

        if ($request->type == Employee::class) {
            $chatUser->employee()->associate($user);
        }

        if ($request->type == Customer::class) {
            $chatUser->customer()->associate($user);
        }

        $chatUser->save();
    }

    /**
     * @throws ChatException
     */
    public function addMessage(CreateMessageDTO $data): Message
    {
        $helper = new MessagesHelper($data->token);
        $chat = $helper->getChat();

        if (!$chat) {
            $helper->createNewChat();
        }

        $file = $data->file ?? null;
        $message = $helper->addMessage($data->message, $data->area, $file);
        $helper->setLastRead();

        return $message;
    }
}
