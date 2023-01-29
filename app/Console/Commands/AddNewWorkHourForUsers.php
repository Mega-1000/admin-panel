<?php

namespace App\Console\Commands;

use App\Jobs\AddNewWorkHourForUsers as UserWorkJob;
use Illuminate\Console\Command;

class AddNewWorkHourForUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:add-work-hours';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new work hours';

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
     */
    public function handle(): void
    {
        // TODO Deprecated - need to be resolved with IoC
        dispatch_now(new UserWorkJob());
    }
}
