<?php

namespace App\Console\Commands;

use App\Jobs\AllegroOrderSynchro;
use Illuminate\Console\Command;

class ImportOrdersFromAllegro extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:allegro';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import zamówień z allegro';

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
        dispatch(new AllegroOrderSynchro());
    }
}
