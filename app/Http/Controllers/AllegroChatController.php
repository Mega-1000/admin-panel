<?php

namespace App\Http\Controllers;

use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Helpers\PaginationHelper;
use App\Entities\AllegroChatThread;
use App\Services\AllegroChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AllegroChatController extends Controller
{

    protected $allegroChatService;

    public function __construct(AllegroChatService $allegroChatService) {
        $this->allegroChatService = $allegroChatService;
    }

    public function chatWindow() {
        return view('allegro.chat-window');
    }

    public function checkUnreadedThreads(Request $request): JsonResponse {

        $chatLastCheck = $request->input('chatLastCheck');

        $areNewMessages = false;
        if($chatLastCheck) {
            $areNewMessages = $this->allegroChatService->areNewMessages($chatLastCheck);
        }

        $unreadedThreads = setting('allegro.unreaded_chat_threads') ?: '[]';

        $response = [
            'unreadedThreads' => json_decode($unreadedThreads),
            'areNewMessages' => $areNewMessages,
        ];

        return response()->json($response);
    }

    public function bookThread(Request $request) {

        $unreadedThreads = $request->input('unreadedThreads');

        if(empty($unreadedThreads)) return response()->json(null);

        $currentThread = $this->allegroChatService->getCurrentThread($unreadedThreads);

        if (empty($currentThread)) return response()->json(null);

        return response()->json($currentThread);
    }

    public function messagesPreview(string $threadId) {

        $allegroPrevMessages = AllegroChatThread::where('allegro_thread_id', $threadId)->where('type', '!=', 'PENDING')->with('user')->get();

        return response()->json($allegroPrevMessages);
    }

    public function getMessages(string $threadId): JsonResponse {
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
        if($allegroPrevMessages->isEmpty() && !$res['messages']) return response()->json(null);

        $newMessages = $this->allegroChatService->insertMsgsToDB($res['messages']);

        $messagesCollection = collect($newMessages);

        if(!$allegroPrevMessages->isEmpty()) $messagesCollection = $allegroPrevMessages->concat($messagesCollection);

        return response()->json($messagesCollection);
    }

    public function getNewMessages(Request $data) {

        $threadId = $data->input('threadId');
        $lastDate = $data->input('lastDate');
        $isPreview = $data->input('isPreview');

        if(!$isPreview) {
            // mark thread as read
            $this->allegroChatService->changeReadFlagOnThread($threadId, [
                'read' => true,
            ]);
        }

        $res = $this->allegroChatService->listMessages($threadId, $lastDate);

        if(!$res['messages']) return response()->json('null', 200);

        $newMessages = collect( $this->allegroChatService->insertMsgsToDB($res['messages']) );

        return response($newMessages);
    }

    public function writeNewMessage(Request $request): JsonResponse {

        $content = $request->input('content');
        $threadId = $request->input('threadId');
        $attachmentId = $request->input('attachmentId');

        $data = [
            'text' => $content,
        ];

        if($attachmentId) {
            $data['attachments'] = [
                [
                    'id' => $attachmentId,
                ]
            ];
        }

        $res = $this->allegroChatService->newMessage($threadId, $data);

        // update current time for prevent unmark msg for consultant
        $currentDate = Carbon::now();
        $currentDateTime = $currentDate->toDateTimeString();

        AllegroChatThread::where([
            'allegro_thread_id' => $threadId,
            'type' => 'PENDING',
        ])->update([
            'updated_at' => $currentDateTime,
        ]);

        if(!$res['id']) return response()->json(null);

        return response()->json('OK');
    }

    public function newAttachmentDeclaration(Request $request) {

        $filename = $request->input('filename');
        $size = $request->input('size');

        $data = [
            'filename' => $filename,
            'size'     => $size,
        ];

        $res = $this->allegroChatService->newAttachmentDeclaration($data);

        if(!$res['id']) return response()->json('null', 200);

        return response($res['id']);
    }

    public function uploadAttachment(string $attachmentId, Request $request) {

        $file = $request->file('file');
        $mimeType = $file->getClientMimeType();
        $contents = file_get_contents($file);

        $res = $this->allegroChatService->uploadAttachment($attachmentId, $contents, $mimeType);

        if(!$res['id']) return response()->json('null', 200);

        return response($res['id']);
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

        $allegroThreads = AllegroChatThread::all()->sortBy('id', SORT_REGULAR, true)->groupBy('allegro_thread_id');
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
