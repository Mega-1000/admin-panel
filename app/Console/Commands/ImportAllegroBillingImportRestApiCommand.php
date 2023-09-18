<?php

namespace App\Console\Commands;

use App\Services\AllegroBillingService;
use Illuminate\Console\Command;

class ImportAllegroBillingImportRestApiCommand extends Command
{
    protected $signature = 'import:allegro-billing-import-rest-api';

    protected $description = 'Importing billing data from allegro REST API';

    public function handle(): void
    {
        $allegroBillingService = app(AllegroBillingService::class);
        $allegroBillingService->getAllBillingsData();

    }
}
