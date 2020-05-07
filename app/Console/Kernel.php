<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(Jobs\CheckStatusInpostPackagesJob::class)->everyMinute();
        $schedule->job(Jobs\CheckPackagesStatusJob::class)->everyMinute();
        $schedule->job(Jobs\ChangeShipmentDatePackagesJob::class)->dailyAt("00:30");
        $schedule->job(Jobs\SendLPWithReminderSendingToWarehouseJob::class)->dailyAt("05:00");
        $schedule->job(Jobs\CheckPriceChangesInProductsJob::class)->dailyAt("04:00");
        $schedule->job(Jobs\CheckDateOfProductNewPriceJob::class)->dailyAt("04:30");
        $schedule->job(Jobs\CustomerOrderDataReminder::class)->dailyAt("09:00");
        $schedule->job(Jobs\Orders\TriggerOrderLabelSchedulersJob::class)->everyFiveMinutes();
        $schedule->job(Jobs\AddNewWorkHourForUsers::class)->dailyAt("00:01");
        $schedule->job(Jobs\CheckTasksFromYesterdayJob::class)->dailyAt("00:01");
        $schedule->job(Jobs\WarehouseDispatchPendingReminderJob::class)->everyFifteenMinutes()->between('9:00', '17:00');
        $schedule->job(Jobs\CheckPromisePaymentsDates::class)->everyMinute();
        $schedule->job(Jobs\ValidateSubiekt::class)->everyFiveMinutes();
        $schedule->job(Jobs\ChangeOrderInvoiceData::class)->dailyAt("07:00");
        $schedule->job(Jobs\JpgGeneratorJob::class)->dailyAt("01:00");
        $schedule->job(Jobs\ImportCsvFileJob::class)->everyMinute();
        $schedule->job(Jobs\ImportOrdersFromSelloJob::class)->cron('0 6,11,17,22 * * *');
        $schedule->job(UpdatePackageRealCostJob::class)->dailyAt("00:30");
        $schedule->job(Jobs\CheckIfInvoicesExistInOrders::class)->dailyAt("07:00");
        $schedule->job(Jobs\UrgentInvoiceRequest::class)->everyFifteenMinutes()->between('9:00', '17:00');
        $schedule->job(Jobs\CheckForHangedChats::class)->cron('0,15,30,45 7-17 * * 1-5');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
