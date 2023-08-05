<?php

namespace App\Console\Commands;

use App\Jobs\EmailSendingJob;
use App\Services\EmailSendingService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends emails from EmailSending model';

    /**
     * Execute the console command.
     *
     * @param EmailSendingJob $emailSendingJob
     * @return int
     */
    public function handle(EmailSendingJob $emailSendingJob): int
    {
        dispatch_now($emailSendingJob);

        return CommandAlias::SUCCESS;
    }
}
