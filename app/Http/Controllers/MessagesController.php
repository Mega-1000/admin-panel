<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\MessagesHelper;
use App\Helpers\Exceptions\ChatException;
use App\Entities\Chat;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $all = false)
    {
        if ($all) {
            $chats = Chat::all();
        } else {
            $chats = Chat::whereHas('messages', function ($q) {
                $q->where('created_at', '>', now()->subDays(30));
            })->get();
        }

        foreach ($chats as $chat) {
            $helper = new MessagesHelper();
            $helper->chatId = $chat->id;
            $helper->currentUserId = $request->user()->id;
            $helper->currentUserType = MessagesHelper::TYPE_USER;
            $chat->title = $helper->getTitle();
            $chat->url = route('chat.show', ['token' => $helper->encrypt()]);
            $chat->lastMessage = $chat->messages()->latest()->first();
        }

        $chats = $chats->all();
        uasort($chats, function ($a, $b) {
            return $a->lastMessage->created_at > $b->lastMessage->created_at ? -1 : 1;
        });
        
        return view('chat.index')->withChats($chats)->withShowAll($all);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($token)
    {
        try {
            $helper = new MessagesHelper($token);
            $chat = $helper->getChat();
            $product = $helper->getProduct();
            $order = $helper->getOrder();
            $helper->setLastRead();
            return view('chat.show')->with([
                'chat' => $chat,
                'product' => $product,
                'order' => $order,
                'title' => $helper->getTitle(),
                'route' => route('api.messages.post-new-message', ['token' => $helper->encrypt()]),
                'routeRefresh' => route('api.messages.get-messages', ['token' => $helper->encrypt()])
            ]);
        } catch (ChatException $e) {
            $e->log();
            return redirect(env('FRONT_URL'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getUrl($mediaId, $postCode, $email)
    {
        try {
            $token = MessagesHelper::getToken($mediaId, $postCode, $email);
            $url = route('chat.show', ['token' => $token]);
            return redirect($url);
        } catch (ChatException $e) {
            $e->log();
            return redirect(env('FRONT_URL'));
        }
    }
}
