<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

        if(!$unreadedThreads || empty($unreadedThreads)) response(null, 500);

        $unreadedThreadsIds = array_column($unreadedThreads, 'id');

        $alreadyOpenedThreads = AllegroChatThread::where([
            'allegro_thread_id' => $unreadedThreadsIds,
            'status'            => 'open',
        ])->pluck('allegro_thread_id')->toArray();

        $alreadyOpenedThreads = array_flip($alreadyOpenedThreads);

        $currentThreadId = null;
        $user = auth()->user();

        foreach($unreadedThreadsIds as $uThreadId) {
            // check if thread is already booked

            if(isset($alreadyOpenedThreads[ $uThreadId ])) continue;

            // prepare temp Allegro Thread for User
            AllegroChatThread::insert([
                'allegro_thread_id'     => $uThreadId,
                'allegro_msg_id'        => 'temp_for_'.$user->id,
                'user_id'               => $user->id,
                'allegro_user_login'    => 'unknown',
                'status'                => 'open',
                'content'               => '',
                'is_outgoing'           => false,
                'type'                  => 'PENDING',
                'original_allegro_date' => '2023-01-01 14:00:00',
            ]);
            $currentThreadId = $uThreadId;
            break;
        }

        return response($currentThreadId);
    }
    public function getMessages(string $threadId) {
        // mark thread as read
        $data = [
            'read' => true
        ];
        $this->allegroChatService->changeReadFlagOnThread($threadId, $data);

        $allegroPrevMessages = AllegroChatThread::where('allegro_thread_id', $threadId)->where('type', '!=', 'PENDING')->get();
        
        if($allegroPrevMessages->isEmpty()) {
            $res = $this->allegroChatService->listMessages($threadId);
        } else {
            $res = $this->allegroChatService->listMessages($threadId, $allegroPrevMessages->last()->original_allegro_date);
        }
        if(!$res['messages']) return response(null, 500);

        $newMessages = [];
        $user = auth()->user();

        foreach($res['messages'] as $msg) {
            $carbon = new Carbon($msg['createdAt']);
            
            $newMessages[] = [
                'allegro_thread_id'     => $msg['thread']['id'],
                'allegro_msg_id'        => $msg['id'],
                'user_id'               => $user->id,
                'allegro_user_login'    => $msg['author']['login'],
                'status'                => 'open',
                'subject'               => $msg['subject'],
                'content'               => $msg['text'],
                'is_outgoing'           => !$msg['author']['isInterlocutor'],
                'attachments'           => json_encode($msg['attachments']),
                'type'                  => $msg['type'],
                'allegro_offer_id'      => $msg['relatesTo']['offer'],
                'allegro_order_id'      => $msg['relatesTo']['order'],
                'original_allegro_date' => $carbon->toDateTimeString(),
            ];
        }
        $successInsert = AllegroChatThread::insert($newMessages);

        if(!$successInsert) return response(null, 500);

        $messagesCollection = collect($newMessages);

        if(!$allegroPrevMessages->isEmpty()) $messagesCollection = $allegroPrevMessages->concat($messagesCollection);

        return response($messagesCollection);
    }
}
