<?php

namespace App\Console\Commands;

use App\Jobs\CheckPackagesStatusJob;
use App\Jobs\CheckStatusInpostPackagesJob;
use Illuminate\Console\Command;

class CheckStatusInpostPackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:inpost';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check status in Inpost packages';

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
        dispatch_now(new CheckPackagesStatusJob());
    }
}
