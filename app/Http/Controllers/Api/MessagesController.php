<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\MessagesHelper;
use App\Helpers\Exceptions\ChatException;

class MessagesController extends Controller
{
    public function getUrl($mediaId, $postCode, $email)
    {
        try {
            $token = MessagesHelper::getToken($mediaId, $postCode, $email);
            $url = route('chat.show', ['token' => $token]);
            return response($url);
        } catch (ChatException $e) {
            $e->log();
            return response($e->getMessage(), 400);
        }
    }

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
            return response(['messages' => $out]);
        } catch (ChatException $e) {
            $e->log();
            return response($e->getMessage(), 400);
        }
    }
}
