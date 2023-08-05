<?php

namespace App\Console\Commands;

use App\Services\AllegroBillingService;
use Illuminate\Console\Command;

class ImportBillingFromAllegro extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:billing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(
        AllegroBillingService $allegroBillingService
    )
    {
        // get billing operations from allegro api
        $operations = $allegroBillingService->getBillingEntriesFromYesterday();

        // create DTOs from billing operations

        // import

        return Command::SUCCESS;
    }
}
