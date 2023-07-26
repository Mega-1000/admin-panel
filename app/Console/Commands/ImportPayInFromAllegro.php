<?php

namespace App\Console\Commands;

use App\Jobs\ImportPayInFromAllegroJob;
use App\Mail\TestMail;
use Illuminate\Console\Command;
use Illuminate\Mail\Mailer;

class ImportPayInFromAllegro extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importuj transakcje z Allegro';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Mailer::create()->to('pawbud6969@gmail.com')->send(new TestMail());

        ImportPayInFromAllegroJob::dispatchNow();

        return Command::SUCCESS;
    }
}
