<?php

namespace App\Console\Commands;

use App\Entities\OrderLabel;
use App\Jobs\AddLabelJob;
use App\Jobs\RemoveLabelJob;
use App\Mail\HtmlMessageMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendInvoiceErrorEmail extends Command
{
	protected $signature = 'invoice:error_email';
	
	protected $description = 'Send wrong invoice message';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function handle()
	{
		$message = "Przepraszamy ale system informatyczny mógł wysłać błednie informacje na temat wystawiana faktury w najblizszym czasie.<br/>
Błąd powstał w związku z tym iż byliście w przeszłosci naszym klinetem który zakupil bądź dostał oferte na zakup towaru a system potraktował wszystkie takie zapytania jako ofery zrealizowane i wysłal proforme do sprawdzenia czy wszystkie dane na niej sa poprawne.<br/>
Oczywiście prosimy nic nie dokonywać tyklo skakować tego e maila poniważ żadne czynności oczywiście nie będą doknywane w związku z tą proformą.<br/>
Zakup badż oferta mógła być dokonywana na firme MEGA1000  bądź na ELEKTRONICZNA PLATFORME HANDLOWĄ<br/>
<br/>
<br/>
Przepraszamy za kłopot i z pozdrowieniami<br/>
w razie niejasnosci prosimy dzwonic 691801594 bądź pisac info@mega1000.pl";
		$subject = "Powiadomienie";
		
		$orders = OrderLabel::with('order')->confirmationSended()->get();
		foreach ($orders as $orderLabel) {
			$mail_to = $orderLabel->order->customer->login;
			try {
				\Mailer::create()
					->to($mail_to)
					->send(new HtmlMessageMail($message, $subject));
				dispatch_now(new RemoveLabelJob($orderLabel->order_id, [194]));
				
				/**
				 * maybe to create label with id=200 to keep this orders?
				 */
				//dispatch_now(new AddLabelJob($orderLabel->order_id, [200]));
			} catch (\Exception $e) {
				Log::error('Send email failed', [$e]);
			}
		}
	}
}
