<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Entities\AllegroChatThread;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AllegroUnlockInactiveThreads implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $unreadedThreads = [];

    public function handle()
    {
        $currentDate = Carbon::now();
        $currentDateTime = $currentDate->subMinutes(15)->toDateTimeString();

        $deleted = AllegroChatThread::where([
            ['type', '=', 'PENDING'],
            ['updated_at', '<', $currentDateTime],
        ])->delete();

        Log::channel('allegro_chat')->info("Deleted Pending Threads: " . json_encode($deleted));
    }
}
