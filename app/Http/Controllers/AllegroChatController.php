<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entities\AllegroChatThread;
use App\Services\AllegroChatService;

class AllegroChatController extends Controller
{

    protected $allegroChatService;

    public function __construct(AllegroChatService $allegroChatService) {
        $this->allegroChatService = $allegroChatService;
    }

    public function checkUnreadedThreads() {
        $unreadedThreads = setting('allegro.unreaded_chat_threads');

        return response($unreadedThreads);
    }
    public function bookThread(Request $request) {

        $unreadedThreads = $request->input('unreadedThreads');

        if(!$unreadedThreads || empty($unreadedThreads)) response('empty', 500);

        $unreadedThreadsIds = array_column($unreadedThreads, 'id');

        $alreadyOpenedChats = AllegroChatThread::where([
            'allegro_thread_id' => $unreadedThreadsIds,
            'status'            => 'open',
        ])->get();

        $currentThreadId = 'empty';

        foreach($unreadedThreadsIds as $uThreadId) {
            // check if thread is already booked
            $isChatAlreadyBooked = $alreadyOpenedChats->search(function($chat) use ($uThreadId) {
                $chat->allegro_thread_id == $uThreadId;
            });
            if($isChatAlreadyBooked) continue;

            $user = auth()->user();

            // prepare temp Allegro Thread for User
            AllegroChatThread::insert([
                'allegro_thread_id' => $uThreadId,
                'allegro_msg_id'    => 'temp_for_'.$user->id,
                'user_id'           => $user->id,
                'allegro_user_login' => 'unknown',
                'status' => 'open',
                'content' => '',
                'is_outgoing' => false,
                'type' => 'empty',
                'original_allegro_date' => '2023-01-01 14:00:00',
            ]);
            $currentThreadId = $uThreadId;
            break;
        }

        return response($currentThreadId);
    }
    public function getMessages(string $threadId) {
        $data = [
            'read' => true
        ];
        $this->allegroChatService->changeReadFlagOnThread($threadId, $data);

        $messages = $this->allegroChatService->listMessages($threadId);
        
        return response('$messages');
    }
}
