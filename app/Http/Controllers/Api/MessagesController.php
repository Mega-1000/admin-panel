<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\MessagesHelper;

class MessagesController extends Controller
{
    public function getUrl($mediaId, $postCode, $email)
    {
        try {
            $token = MessagesHelper::getToken($mediaId, $postCode, $email);
            $url = route('chat.show', ['token' => $token]);
            return response($url);
        } catch (\Exception $e) {
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
                throw new \Exception('User not allowed to send message');
            }
            $helper->addMessage($request->message);
            return response('ok');
        } catch (\Exception $e) {
            \Log::error('Trying to access chat: '.$e->getMessage());
            return response($e->getMessage(), 400);
        }
    }
}
