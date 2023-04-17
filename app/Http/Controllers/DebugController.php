<?php

namespace App\Http\Controllers;

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
        $ess = new EmailSendingService();
        $es = EmailSending::find(2);
        $ess->sendEmail($es, Carbon::now());
    }
}
