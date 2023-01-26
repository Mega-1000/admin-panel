<?php

namespace App\Services;

use Illuminate\Support\Carbon;

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
}