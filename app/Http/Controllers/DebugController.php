<?php

namespace App\Http\Controllers;

use App\Entities\ChatUser;
use App\Entities\EmailSending;
use App\Services\EmailSendingService;
use Illuminate\Support\Carbon;

class DebugController extends Controller
{
    public function index()
    {
        if (config('app.env') == 'production') {
            return null;
        }
        
    }
}
