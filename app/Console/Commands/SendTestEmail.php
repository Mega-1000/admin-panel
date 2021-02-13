<?php

namespace App\Console\Commands;

use App\Jobs\TestEmailJob;
use Illuminate\Console\Command;

class SendTestEmail extends Command
{
    protected $signature = 'test:email';

    protected $description = 'Sends test email';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        dispatch(new TestEmailJob);
    }
}
