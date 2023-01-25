<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Helpers\PaginationHelper;
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
            'type'              => 'PENDING',
        ])->pluck('allegro_thread_id')->toArray();

        $alreadyOpenedThreads = array_flip($alreadyOpenedThreads);

        $currentThread = null;
        $user = auth()->user();

        foreach($unreadedThreads as $uThread) {
            // check if thread is already booked
            if(isset($alreadyOpenedThreads[ $uThread['id'] ])) continue;

            // prepare temp Allegro Thread for User
            AllegroChatThread::insert([
                'allegro_thread_id'     => $uThread['id'],
                'allegro_msg_id'        => 'temp_for_'.$user->id,
                'user_id'               => $user->id,
                'allegro_user_login'    => 'unknown',
                'content'               => '',
                'is_outgoing'           => false,
                'type'                  => 'PENDING',
                'original_allegro_date' => '2023-01-01 14:00:00',
            ]);
            $currentThread = $uThread;
            break;
        }

        return response()->json($currentThread);
    }
    public function messagesPreview(string $threadId) {

        $user = auth()->user();

        if($user->role_id != 1) return response(null, 500);

        $allegroPrevMessages = AllegroChatThread::where('allegro_thread_id', $threadId)->where('type', '!=', 'PENDING')->with('user')->get();

        return response()->json($allegroPrevMessages);
    }
    public function getMessages(string $threadId) {
        // mark thread as read
        $data = [
            'read' => true
        ];
        $this->allegroChatService->changeReadFlagOnThread($threadId, $data);

        $allegroPrevMessages = AllegroChatThread::where('allegro_thread_id', $threadId)->where('type', '!=', 'PENDING')->with('user')->get();
        if($allegroPrevMessages->isEmpty()) {
            $res = $this->allegroChatService->listMessages($threadId);
        } else {
            $res = $this->allegroChatService->listMessages($threadId, $allegroPrevMessages->last()->original_allegro_date);
        }
        if($allegroPrevMessages->isEmpty() && !$res['messages']) return response(null, 500);
        
        $messages = array_reverse($res['messages']);

        $newMessages = [];
        $user = auth()->user();

        foreach($messages as $msg) {
            $carbon = new Carbon($msg['createdAt']);
            
            $newMessages[] = [
                'allegro_thread_id'     => $msg['thread']['id'],
                'allegro_msg_id'        => $msg['id'],
                'user_id'               => $user->id,
                'allegro_user_login'    => $msg['author']['login'],
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

        // add missing informations about user
        foreach ($newMessages as &$newMessage) {
            $newMessage['user'] = [
                'name'  => $user->name,
                'email' => $user->email,
            ];
        }

        $messagesCollection = collect($newMessages);

        if(!$allegroPrevMessages->isEmpty()) $messagesCollection = $allegroPrevMessages->concat($messagesCollection);

        return response($messagesCollection);
    }
    
    public function downloadAttachment(string $attachmentId) {
        $res = $this->allegroChatService->downloadAttachment($attachmentId);

        return response()->json($res);
    }

    public function exitChat(string $threadId) {
        AllegroChatThread::where('allegro_thread_id', $threadId)->where('type', 'PENDING')->delete();

        return response()->json(['success' => true]);
    }

    public function getAllChats(int $currentPage = 1) {

        $allegroThreads = AllegroChatThread::all()->groupBy('allegro_thread_id');
        $perPage = 20;
        $allegroThreadsChunk = PaginationHelper::paginateModelsGroupBy($allegroThreads, $currentPage, $perPage);

        $numberOfPages = $allegroThreadsChunk['numberOfPages'];
        $threadsList = [];
        foreach($allegroThreadsChunk['chunk'] as $thread) {
            foreach($thread as $msg) {
                if(!$msg['is_outgoing']) {
                    $threadsList[] = $msg;
                    break;
                }
            }
        }

        $prevPageUrl = route('pages.getAllChats', ['currentPage' => ($currentPage - 1)]);
        $nextPageUrl = route('pages.getAllChats', ['currentPage' => ($currentPage + 1)]);

        return view('allegro.all-chats', compact('threadsList', 'numberOfPages', 'currentPage', 'prevPageUrl', 'nextPageUrl'));
    }
}
