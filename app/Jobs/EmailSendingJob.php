<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Services\EmailSendingService;

class EmailSendingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailSendingService;

    public function __construct(EmailSendingService $emailSendingService)
    {
        $this->emailSendingService = $emailSendingService;
    }

    public function handle()
    {
        $this->emailSendingService->sendScheduledEmail();
    }
}
