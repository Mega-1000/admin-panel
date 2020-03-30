<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdatePackageRealCostJob;

class UpdatePackageRealCost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:realcost';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import realnych cen paczek z csv';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
         dispatch_now(new UpdatePackageRealCostJob());
    }
}
