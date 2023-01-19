<?php

namespace App\Services;
use App\Http\Controllers\Api\ApiResponsesTrait;

class AllegroChatService extends AllegroApiService {

    use ApiResponsesTrait;
    
    protected $auth_record_id = 3;

    public function __construct() {
        parent::__construct();
    }

    // https://developer.allegro.pl/documentation#operation/listThreadsGET
    public function listUserThreads() {
        $url = $this->getRestUrl('/messaging/threads');
        $response = $this->request('GET', $url, []);
        if(!$response) return $this->createdErrorResponse('Something went wrong!');
    }
}