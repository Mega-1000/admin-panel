<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class SetLogsPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set-logs-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info( 'Setting permissions for storage/logs' );
        $this->info( shell_exec( 'chmod -R 777 storage/logs' ) );
        $this->info( 'Done' );

        return CommandAlias::SUCCESS;
    }
}
