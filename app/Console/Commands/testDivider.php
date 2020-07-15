<?php

namespace App\Console\Commands;

use App\Entities\ConfirmPackages;
use App\Entities\Order;
use App\Entities\OrderPackage;
use App\Entities\OrderPackageProduct;
use App\Entities\OrderPayment;
use App\Entities\Product;
use App\Helpers\PackageDivider;
use App\Integrations\GLS\GLSClient;
use App\Jobs\ConfirmSentPackagesJob;
use App\Mail\MessageSent;
use App\Mail\OrderStatusChangedToDispatchMail;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class testDivider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'divider';

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
    public function handle()
    {

        echo 'test' . Storage::disk('private')->path('labels/gls/');
    }
}
