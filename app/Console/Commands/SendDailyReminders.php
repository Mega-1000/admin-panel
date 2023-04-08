<?php

namespace App\Console\Commands;

use App\Entities\Order;
use App\Facades\Mailer;
use App\Mail\SendReminderAboutOffer;
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // get all orders witch have label of id 224 keep in mind that ther is pivot table called order_labels and it has order_id and label_id
        $orders = Order::whereHas('labels', function ($query) {
            $query->where('label_id', 224);
        })->get();

        foreach ($orders as $order) {
            Mailer::create()
                ->to($order->customer->email)
                ->send(new SendReminderAboutOffer($order->customer));
        }

        return Command::SUCCESS;
    }
}
