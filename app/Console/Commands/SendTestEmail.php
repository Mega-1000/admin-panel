<?php

namespace App\Console\Commands;

use App\Mail\TestMail;
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
        \Mailer::create()
            ->to('paweljar@gmail.com')
            ->send(new TestMail());
    }
}
