<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use TCG\Voyager\Models\Setting;
use Illuminate\Support\Facades\Log;
use App\Services\AllegroChatService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class AllegroSaveUnreadedChatThreads implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $unreadedThreads = [];

    public function __construct()
    {
        //
    }

    public function handle()
    {
        Log::info("Start AllegroSaveUnreadedChatThreads!");

        $hasUnreadedMessages = true;
        $offset = 0;
        // continue until unreadedThreads will fill with all unreaded threads
        while ($hasUnreadedMessages) {
            $hasUnreadedMessages = $this->getThreads($offset);
            $offset += 20;
        }

        // save as temporary settings (can store up to about 200 records)
        // so now all consultants have sync and actual data
        Setting::updateOrCreate([
            'key' => 'allegro.unreaded_chat_threads'
        ], [
            'key' => 'allegro.unreaded_chat_threads',
            'display_name' => 'Temporary Allegro unreaded messages',
            'value' => json_encode($this->unreadedThreads),
            'type' => 'allegro.unreaded_chat_threads',
            'order' => 1,
            'group' => 'AllegroChat',
        ]);
        
        Log::info("Unreaded Threads: ".json_encode($this->unreadedThreads));
    }

    private function getThreads($offset)
    {
        $allegroChatService = new AllegroChatService();
        $allegroThreads = $allegroChatService->listThreads($offset);
        // if all of 20 downloaded threads are not reads
        $unreadedThreads = 0;
        $totalThreads = count($allegroThreads['threads']);

        foreach ($allegroThreads['threads'] as $thread) {
            if (!$thread['read']) {
                $unreadedThreads++;
                $this->unreadedThreads[] = $thread;
            }
        }

        return ($unreadedThreads == $totalThreads);
    }
}
