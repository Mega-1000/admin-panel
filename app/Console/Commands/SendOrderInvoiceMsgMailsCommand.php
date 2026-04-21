<?php

namespace App\Console\Commands;

use App\Jobs\Cron\SendOrderInvoiceMsgMailsJob;
use Illuminate\Console\Command;

/**
 * Class SendOrderInvoiceMsgMailsJob
 * @package App\Jobs
 */
class SendOrderInvoiceMsgMailsCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'mail:order-invoice-message';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send order invoice message';
	
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
	    dispatch_now(new SendOrderInvoiceMsgMailsJob());
    }
}
