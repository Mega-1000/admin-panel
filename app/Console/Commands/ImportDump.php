<?php

namespace App\Console\Commands;

use DumpedTablesSeeder;
use Illuminate\Console\Command;

class ImportDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import dumped file from json';

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
        $seeder = new DumpedTablesSeeder();
        $seeder->run();
    }
}
