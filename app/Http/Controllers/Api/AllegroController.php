<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AllegroChatService;

class AllegroController extends Controller {

    protected $allegroChatService;
    
    public function __construct(AllegroChatService $allegroChatService) {
        $this->allegroChatService = $allegroChatService;
    }

    // get messages threads from Allegro
    public function listUserThreads() {
        $res = $this->allegroChatService->listUserThreads();
        
        if(!$res) response('Something went wrong!', 500);

        response()->json($res);
    }
}
