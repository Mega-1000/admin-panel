<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class AllegroChatService extends AllegroApiService {

    // https://developer.allegro.pl/documentation#tag/Message-Center
    protected $auth_record_id = 3;

    public function __construct() {
        parent::__construct();
    }
    public function listThreads(int $offset = 0) {
        $url = $this->getRestUrl('/messaging/threads');

        $data = [
            'offset' => $offset
        ];

        $response = $this->request('GET', $url, $data);

        return $response;
    }
    public function listMessages(string $threadId, $after = null) {

        $data = [];
        if($after) {
            $carbon = new Carbon($after);

            $data = [
                'after' => $carbon->toISOString(),
            ];
        }
        $url = $this->getRestUrl("/messaging/threads/{$threadId}/messages");
        $response = $this->request('GET', $url, $data);

        return $response;
    }
    public function downloadAttachment(string $attachmentId) {
        $url = $this->getRestUrl("/messaging/message-attachments/{$attachmentId}");
        $response = $this->request('GET', $url, []);

        $path = sys_get_temp_dir().'/'.base64_encode($url);
        file_put_contents($path, (string) $response->getBody());

        return $path;
    }
    public function newMessage(array $data) {
        $url = $this->getRestUrl("/messaging/messages");
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
    public function uploadAttachment(string $attachmentId, string $contentsFile) {

        $attachment = [
            'name' => 'file',
            'contents' => ($contentsFile),
            'filename' => $attachmentId
        ];

        $url = $this->getRestUrl("/messaging/message-attachments/{$attachmentId}");
        $response = $this->request('PUT', $url, [], $attachment);

        return $response;
    }
}