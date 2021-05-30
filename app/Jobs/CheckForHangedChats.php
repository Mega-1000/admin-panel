<?php

namespace App\Jobs;

use App\Entities\Chat;
use App\Entities\Label;
use App\Entities\Message;
use App\Helpers\MessagesHelper;
use App\Helpers\OrderLabelHelper;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use function Clue\StreamFilter\fun;

class CheckForHangedChats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $yellowLabelTime = Carbon::now();
        $yellowLabelTime->addHours(3);
        $redLabelTime = Carbon::now();
        $redLabelTime->addHours(5);
        $chats = Chat::whereNotNull('order_id')
            ->with(['messages' => function ($q) {
                $q->with('chatUser');
            }])->get();
        $chats->map(function ($chat) use ($redLabelTime, $yellowLabelTime) {
            $lastMessage = $chat->getLastMessage();
            if (!$this->validate($lastMessage)) {
                return;
            }
            $this->setLabels($redLabelTime, $lastMessage, $chat, $yellowLabelTime);
        });
    }

    private function validate($lastMessage)
    {
        return $lastMessage && !empty($lastMessage->chatUser->customer_id);
    }

    private function setLabels(Carbon $redLabelTime, $lastMessage, $chat, Carbon $yellowLabelTime): void
    {
        if ($redLabelTime->greaterThan($lastMessage->created_at)) {
            OrderLabelHelper::setRedLabel($chat);
        } else if ($yellowLabelTime->greaterThan($lastMessage->created_at)) {
            OrderLabelHelper::setYellowLabel($chat);
        }
    }
}
