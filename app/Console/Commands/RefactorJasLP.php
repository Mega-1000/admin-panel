<?php

namespace App\Console\Commands;

use App\Repositories\OrderPackageRepository;
use Illuminate\Console\Command;

class RefactorJasLP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jas:refactor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle(OrderPackageRepository $repository)
    {
        $packages = $repository->findWhere([['delivery_courier_name', '=', 'JAS'],['letter_number', '!=', null]]);

        foreach($packages as $package){
            $package->update([
                'letter_number' => $package->sending_number,
                'sending_number' => $package->letter_number
            ]);
        }
    }
}
