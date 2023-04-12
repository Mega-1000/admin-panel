<?php

namespace App\Console\Commands;

use App\Entities\Order;
use App\Facades\Mailer;
use App\Mail\SendReminderAboutOffer;
use App\Repositories\Orders;
use Illuminate\Console\Command;

class SendDailyReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:daily-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily reminders about offer to customers with special label';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $orders = Orders::getOrdersWithoutReminderForLabel(224);


        foreach ($orders as $order) {
            Mailer::create()
                ->to($order->customer->login)
                ->send(new SendReminderAboutOffer($order->customer));
        }

        return Command::SUCCESS;
    }
}
