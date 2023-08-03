<?php

namespace App\Console\Commands;

use App\Services\AllegroImportPayInService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

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
    public function handle(): int
    {
        $payInService = app(AllegroImportPayInService::class);
        $payInService->importLastDayPayInsFromAllegroApi();

        return CommandAlias::SUCCESS;
    }
}
