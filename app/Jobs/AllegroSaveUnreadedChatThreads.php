<?php

namespace App\Jobs;

use App\Services\AllegroChatUserManagmentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use TCG\Voyager\Models\Setting;
use Illuminate\Support\Facades\Log;
use App\Services\AllegroChatService;
use App\Services\EmailSendingService;
use Illuminate\Support\Carbon;

class AllegroSaveUnreadedChatThreads extends Job implements ShouldQueue
{

    public $unreadedThreads = [];

    public function __construct()
    {
        //
    }

    public function handle()
    {
        Log::channel('allegro_chat')->info("Start AllegroSaveUnreadedChatThreads!");

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

        Log::channel('allegro_chat')->info("Unreaded Threads: ".json_encode($this->unreadedThreads));
    }

    private function getThreads($offset): bool
    {
        $allegroChatService = new AllegroChatService();
        $allegroThreads = $allegroChatService->listThreads($offset);
        // if all of 20 downloaded threads are not reads
        $unreadedThreads = 0;
        $totalThreads = count($allegroThreads['threads']);

        $currentTime = Carbon::now();
        $minutesAgo = $currentTime->subMinutes(3)->toISOString();

        foreach ($allegroThreads['threads'] as $thread) {
            if (!$thread['read']) {
                $unreadedThreads++;
                $this->unreadedThreads[] = $thread;

                if($thread['lastMessageDateTime'] > $minutesAgo) {
                    $emailSendingService = new EmailSendingService();

                    // confirm that last msg from Allegro isn't from EPH
                    $messagesList = $allegroChatService->listMessages($thread['id']);
                    $lastMsgLogin = $messagesList['messages'][0]['author']['login'];

                    if( $lastMsgLogin === config('app.allegro_login') ) continue;

                    $customer = AllegroChatUserManagmentService::createOrFindUserFromAllegro( $lastMsgLogin );

                    $emailSendingService->addAllegroMsg($thread['id'], $customer->login);
                }
            }
        }

        return ($unreadedThreads == $totalThreads);
    }
}
