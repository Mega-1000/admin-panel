<?php

namespace App\Console\Commands;

use App\Jobs\ImportPayInFromAllegroJob;
use Illuminate\Console\Command;

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
        ImportPayInFromAllegroJob::dispatchNow();

        return Command::SUCCESS;
    }
}
