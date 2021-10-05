<?php

namespace App\Console\Commands;

use App\Jobs\Cron\SendFinalProformConfirmationMailsJob;
use Illuminate\Console\Command;

/**
 * Class SendFinalProformConfirmationMailsJob
 * @package App\Jobs
 */
class SendFinalProformConfirmationMailsCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'mail:final-proform-confirmation';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send final proform confirmation mails';
	
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
	    dispatch_now(new SendFinalProformConfirmationMailsJob());
    }
}
