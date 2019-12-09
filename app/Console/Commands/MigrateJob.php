<?php

namespace App\Console\Commands;

use App\Jobs\AutomaticMigration;
use Illuminate\Console\Command;

class MigrateJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automatic:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migruje zmiany w śledzonych tabelach';

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
        dispatch_now(new AutomaticMigration());
    }
}
