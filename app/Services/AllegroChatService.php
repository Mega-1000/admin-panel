<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use App\Entities\AllegroChatThread;
use Illuminate\Support\Facades\Log;

class AllegroChatService extends AllegroApiService {

    // https://developer.allegro.pl/documentation#tag/Message-Center
    protected $auth_record_id = 2;

    public function __construct() {
        parent::__construct();
    }
    public function listThreads(int $offset = 0) {

        $data = [
            'offset' => $offset
        ];

        $url = $this->getRestUrl('/messaging/threads?' . http_build_query($data));
        $response = $this->request('GET', $url, []);

        return $response;
    }
    public function listMessages(string $threadId, string $after = null) {

        $data = [];
        if($after) {
            $carbon = new Carbon($after, 'UTC');
            $data = [
                'after' => $carbon->addSecond()->toISOString(),
            ];
        }
        $url = $this->getRestUrl("/messaging/threads/{$threadId}/messages?" . http_build_query($data));
        $response = $this->request('GET', $url, []);

        return $response;
    }
    public function downloadAttachment(string $attachmentId) {
        $url = $this->getRestUrl("/messaging/message-attachments/{$attachmentId}");
        $path = sys_get_temp_dir() . '/' . base64_encode($url);
        $response = $this->request('GET', $url, ['sink' => $path]);

        return ['content' => base64_encode($response->getBody()->getContents()), 'contentType' => $response->getHeader('Content-Type')];
    }
    public function newMessage(string $threadId, array $data) {
        $url = $this->getRestUrl("/messaging/threads/$threadId/messages");
        $response = $this->request('POST', $url, $data);

        return $response;
    }
    public function newAttachmentDeclaration(array $data) {
        $url = $this->getRestUrl("/messaging/message-attachments");
        $response = $this->request('POST', $url, $data);

        return $response;
    }

    public function changeReadFlagOnThread(string $threadId, array $data) {
        $url = $this->getRestUrl("/messaging/threads/{$threadId}/read");
        $response = $this->request('PUT', $url, $data);

        return $response;
    }
    public function uploadAttachment(string $attachmentId, string $contents, string $mimeType) {

        $attachment = [
            'contents' => $contents,
            'mimeType' => $mimeType,
        ];

        $url = $this->getRestUrl("/messaging/message-attachments/{$attachmentId}");
        $response = $this->request('PUT', $url, [], $attachment, true);

        return $response;
    }
    public function getCurrentThread(array $unreadedThreads): array {

        $user = auth()->user();
        $currentThread = [];

        // check if user has any opened threads
        $currentThread = AllegroChatThread::where([
            'user_id' => $user->id,
            'type'    => 'PENDING',
        ])->first();

        if( !empty($currentThread) ) return $currentThread->toArray();

        if( empty($unreadedThreads) ) return $currentThread;

        $unreadedThreadsIds = array_column($unreadedThreads, 'id');

        $alreadyOpenedThreads = AllegroChatThread::where([
            'allegro_thread_id' => $unreadedThreadsIds,
            'type'              => 'PENDING',
        ])->pluck('allegro_thread_id')->toArray();

        // flip for swap allegro_thread_id as key
        $alreadyOpenedThreads = array_flip($alreadyOpenedThreads);

        Log::info("Opened threads: ".json_encode($alreadyOpenedThreads));
        Log::info("Unreaded Threads: ".json_encode($unreadedThreads));

        foreach($unreadedThreads as $uThread) {
            // check if thread is already booked
            if(isset($alreadyOpenedThreads[ $uThread['id'] ])) continue;

            // prepare temp Allegro Thread for User
            $newPendingThread = AllegroChatThread::create([
                'allegro_thread_id'     => $uThread['id'],
                'allegro_msg_id'        => 'temp_for_'.$user->id,
                'user_id'               => $user->id,
                'allegro_user_login'    => $uThread['interlocutor']['login'],
                'content'               => '',
                'subject'               => '',
                'is_outgoing'           => false,
                'type'                  => 'PENDING',
                'original_allegro_date' => '2023-01-01 14:00:00',
            ]);
            $currentThread = $newPendingThread->toArray();
            break;
        }
        return $currentThread;
    }
    public function insertMsgsToDB(array $msgs): array {
        $messages = array_reverse($msgs);

        $newMessages = [];
        $user = auth()->user();

        foreach($messages as $msg) {
            $createdAt = new Carbon($msg['createdAt']);
            $currentDateTime = new Carbon();

            $newMessages[] = [
                'allegro_thread_id'     => $msg['thread']['id'],
                'allegro_msg_id'        => $msg['id'],
                'user_id'               => $user->id,
                'allegro_user_login'    => $msg['author']['login'],
                'subject'               => $msg['subject'] ?: '',
                'content'               => $msg['text'],
                'is_outgoing'           => !$msg['author']['isInterlocutor'],
                'attachments'           => json_encode($msg['attachments'] ?: []),
                'type'                  => $msg['type'],
                'allegro_offer_id'      => $msg['relatesTo']['offer']['id'] ?: '',
                'allegro_order_id'      => $msg['relatesTo']['order']['id'] ?: '',
                'original_allegro_date' => $createdAt->toDateTimeString(),
                'created_at'            => $currentDateTime->toDateTimeString(),
                'updated_at'            => $currentDateTime->toDateTimeString(),
            ];
        }
        AllegroChatThread::insert($newMessages);

        // add missing information about user
        foreach ($newMessages as &$newMessage) {
            $newMessage['user'] = [
                'name'  => $user->name,
                'email' => $user->email,
            ];
        }

        return $newMessages;
    }
}
