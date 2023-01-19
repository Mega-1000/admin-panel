<?php

namespace App\Services;

class AllegroChatService extends AllegroApiService {

    protected $auth_record_id = 3;

    public function __construct() {
        parent::__construct();
    }

    // https://developer.allegro.pl/documentation#operation/listThreadsGET
    public function listUserThreads() {
        $url = $this->getRestUrl('/messaging/threads');
        $response = $this->request('GET', $url, []);

        return $response;
    }
}