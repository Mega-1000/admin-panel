<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\AddNewWorkHourForUsers as UserWorkJob;

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
     * @return mixed
     */
    public function handle()
    {
        dispatch_now(new UserWorkJob());
    }
}
