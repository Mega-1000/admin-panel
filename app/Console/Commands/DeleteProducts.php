<?php

namespace App\Console\Commands;

use App\Jobs\DeleteProductsJob;
use Illuminate\Console\Command;

class DeleteProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete products with -1, -2, -3 etc...';

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
        dispatch_now(new DeleteProductsJob());
    }
}
