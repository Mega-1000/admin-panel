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
        $schedule->job(Jobs\CheckStatusInpostPackagesJob::class)->everyFiveMinutes();
        $schedule->job(Jobs\CheckPackagesStatusJob::class)->everyFifteenMinutes();
        $schedule->job(Jobs\ChangeShipmentDatePackagesJob::class)->dailyAt("00:30");
        $schedule->job(Jobs\AllegroTrackingNumberUpdater::class)->dailyAt("02:00");
        $schedule->job(Jobs\SendLPWithReminderSendingToWarehouseJob::class)->dailyAt("05:00");
        $schedule->job(Jobs\CheckPriceChangesInProductsJob::class)->dailyAt("04:00");
        $schedule->job(Jobs\CheckDateOfProductNewPriceJob::class)->dailyAt("04:30");
        // $schedule->job(Jobs\CustomerOrderDataReminder::class)->dailyAt("09:00");
        $schedule->job(Jobs\Orders\TriggerOrderLabelSchedulersJob::class)->everyFiveMinutes();
//        $schedule->job(Jobs\AddNewWorkHourForUsers::class)->dailyAt("00:01");
        $schedule->job(Jobs\CheckTasksFromYesterdayJob::class)->dailyAt("00:01");
        $schedule->job(Jobs\WarehouseDispatchPendingReminderJob::class)->everyThirtyMinutes()->between('9:00', '17:00');
        $schedule->job(Jobs\CheckPromisePaymentsDates::class)->everyThirtyMinutes(); // @TODO this task is very slow, for now
        // i am changing it from everyMinute to everyThirtyMinutes as rewriting would take some time, this should solve
        // queue overload issues
        $schedule->job(Jobs\ValidateSubiekt::class)->everyFiveMinutes();
        // $schedule->job(Jobs\ChangeOrderInvoiceData::class)->dailyAt("07:00");
        // $schedule->job(Jobs\JpgGeneratorJob::class)->dailyAt("01:00");
        $schedule->job(Jobs\ImportCsvFileJob::class)->everyFiveMinutes();
        $schedule->job(Jobs\ImportOrdersFromSelloJob::class)->cron('0 6,11,17,22 * * *');
        $schedule->job(Jobs\UpdatePackageRealCostJob::class)->dailyAt("00:30");
        // $schedule->job(Jobs\CheckIfInvoicesExistInOrders::class)->dailyAt("07:00");
        // $schedule->job(Jobs\UrgentInvoiceRequest::class)->everyFifteenMinutes()->between('9:00', '17:00');
        $schedule->job(Jobs\CheckForHangedChats::class)->cron('0,15,30,45 7-17 * * 1-5');
        $schedule->job(Jobs\ConfirmSentPackagesJob::class)->dailyAt("23:34");
        $schedule->job(Jobs\AutomaticallyFinishOrdersJob::class)->everyFifteenMinutes();
//        $schedule->job(Jobs\ChangeDdpShipmentDatePackagesJob::class)->dailyAt("12:01");
        
        $schedule->job(Jobs\SendMessagesOnNewAllegroOrders::class)->everyFifteenMinutes();
        $schedule->job(Jobs\UpdateAllegroDisputes::class)->everyFifteenMinutes();
        $schedule->job(Jobs\GetNewAllegroDisputesJob::class)->everyFifteenMinutes();

	    $schedule->job(Jobs\Cron\SendOrderInvoiceMsgMailsJob::class)->dailyAt("09:00");
	    $schedule->job(Jobs\Cron\SendInvoicesMailsJob::class)->dailyAt("23:45");
	    $schedule->job(Jobs\AllegroOrderSynchro::class)->everyTenMinutes();
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
