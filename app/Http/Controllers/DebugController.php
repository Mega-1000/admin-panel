<?php

namespace App\Http\Controllers;

use App\Entities\EmailSending;
use App\Services\EmailSendingService;
use Illuminate\Support\Carbon;

class DebugController extends Controller
{
    public function index()
    {
        if (env('APP_ENV') == 'production') {
            return null;
        }
        $ess = new EmailSendingService();
        $es = EmailSending::find(2);
        $ess->sendEmail($es, Carbon::now());
    }
}
