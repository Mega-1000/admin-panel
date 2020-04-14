<?php

namespace App\Http\Controllers\Api;

use App\Entities\ChatUser;
use App\Entities\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\MessagesHelper;
use App\Helpers\Exceptions\ChatException;

class MessagesController extends Controller
{
    public function postNewMessage(Request $request, $token)
    {
        try {
            $helper = new MessagesHelper($token);
            $chat = $helper->getChat();
            if (!$chat) {
                $helper->createNewChat();
            }
            if (!$helper->canUserSendMessage()) {
                throw new ChatException('User not allowed to send message');
            }
            $helper->addMessage($request->message);
            $helper->setLastRead();
            return response('ok');
        } catch (ChatException $e) {
            $e->log();
            return response($e->getMessage(), 400);
        }
    }

    public function addUser(Request $request, $token)
    {
        try {
            $helper = new MessagesHelper($token);
            $chat = $helper->getChat();
            $employee = Employee::findOrFail($request->employee_id);
            if (!$chat) {
                throw new ChatException('Podany czat nie istnieje');
            }
            $chatUser = new ChatUser();
            $chatUser->chat()->associate($chat);
            $chatUser->employee()->associate($employee);
            $chatUser->save();
            return response('ok');
        } catch (ChatException $e) {
            $e->log();
            return response($e->getMessage(), 400);
        }
    }
    public function removeUser(Request $request, $token)
    {
        try {
            $helper = new MessagesHelper($token);
            $chat = $helper->getChat();
            if (!$chat) {
                throw new ChatException('Podany czat nie istnieje');
            }
            $chatUser = ChatUser::findOrFail($request->user_id);
            $chatUser->delete();
            return response('ok');
        } catch (ChatException $e) {
            $e->log();
            return response($e->getMessage(), 400);
        }
    }

    public function getMessages(Request $request, $token)
    {
        try {
            $helper = new MessagesHelper($token);
            $chat = $helper->getChat();
            if (!$chat) {
                throw new ChatException('Wrong chat token');
            }
            $out = '';
            foreach ($chat->messages as $message) {
                if ($message->id <= $request->lastId) {
                    continue;
                }
                $out .= view('chat/single_message')->withMessage($message)->render();
            }
            $helper->setLastRead();

            return response(['messages' => $out, 'users' => $chat->chatUsers]);
        } catch (ChatException $e) {
            $e->log();
            return response($e->getMessage(), 400);
        }
    }

    public function getHistory(Request $request)
    {
        $user = $request->user();
        $out = [];
        foreach ($user->chats as $chat) {
            $helper = new MessagesHelper();
            $helper->chatId = $chat->id;
            $helper->currentUserId = $user->id;
            $helper->currentUserType = MessagesHelper::TYPE_CUSTOMER;
            $out[] = [
                'title' => $helper->getTitle(),
                'url' => route('chat.show', ['token' => $helper->encrypt()]),
                'new_message' => $helper->hasNewMessage()
            ];
        }
        return response($out);
    }

    public function getUrl(Request $request)
    {
        try {
            $url = \App\Http\Controllers\MessagesController::getChatUrl($request->mediaId,
                $request->postCode,
                $request->email,
                $request->phone);
        } catch (ChatException $exception) {
            $exception->log();
            if ($exception->getMessage() == 'wrong_password') {
                return response(['errorMessage' => 'Podany email istnieje w naszej bazie. Proszę podać prawidłowe hasło/numer telefonu'], 400);
            } else {
                return response(['errorMessage' => 'Wystąpil błąd. Spróbuj ponownie później'], 400);
            }
        }
        return response(['url' => $url], 200);
    }
}
