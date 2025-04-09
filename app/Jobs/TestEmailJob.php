<?php declare(strict_types=1);

namespace App\Jobs;

use App\Facades\Mailer;
use App\Http\Controllers\Auth\LoginController;
use App\Mail\TestMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Illuminate\Support\Facades\Log;

class TestEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public function handle()
    {
        Log::info('TestEmailJob started');
        Mailer::notification()
            ->to('bartosz.woszczak@gmail.com')
            ->send(new TestMail());
        Log::info('TestEmailJob send');
    }
}
