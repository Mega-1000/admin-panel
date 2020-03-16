<?php

namespace App\Console\Commands;

use App\Jobs\ImportOrdersFromSelloJob;
use Illuminate\Console\Command;

class ImportOrdersFromSello extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:sello';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import zamówień z allegro przy pomocy Sello';

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
        dispatch_now(new ImportOrdersFromSelloJob());
    }
}
