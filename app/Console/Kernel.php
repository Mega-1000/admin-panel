<?php

namespace App\Console;

use App\Jobs;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
     * @param Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(Jobs\CheckStatusInpostPackagesJob::class)->everyFiveMinutes();

        $schedule->job(Jobs\CheckPackagesStatusJob::class)->everyFiveMinutes()->between('13:00', '16:00');
        $schedule->job(Jobs\CheckPackagesStatusJob::class)->everyFifteenMinutes()->unlessBetween('16:00', '13:00');

//        $schedule->job(Jobs\ChangeShipmentDatePackagesJob::class)->dailyAt("00:30");
        //$schedule->job(Jobs\AllegroTrackingNumberUpdater::class)->dailyAt("02:00");
        $schedule->job(Jobs\SendLPWithReminderSendingToWarehouseJob::class)->dailyAt("05:00");
        $schedule->job(Jobs\CheckPriceChangesInProductsJob::class)->dailyAt("04:00");
        $schedule->job(Jobs\CheckDateOfProductNewPriceJob::class)->dailyAt("04:30");
        // $schedule->job(Jobs\CustomerOrderDataReminder::class)->dailyAt("09:00");
        $schedule->job(Jobs\Orders\TriggerOrderLabelSchedulersJob::class)->everyFiveMinutes();
//        $schedule->job(Jobs\AddNewWorkHourForUsers::class)->dailyAt("00:01");
        $schedule->job(Jobs\CheckTasksFromYesterdayJob::class)->dailyAt("00:01");
        $schedule->job(Jobs\WarehouseDispatchPendingReminderJob::class)->everyThirtyMinutes()->between('9:00', '17:00');

        // monday to saturday between 7 - 19
        $schedule->job(Jobs\CheckNotificationsMailbox::class)->cron('*/15 7-19 * * 1-6');

        // monday to saturday between 6 - 24
        $schedule->job(Jobs\AllegroSaveUnreadedChatThreads::class)->everyThreeMinutes();
        $schedule->job(Jobs\AllegroUnlockInactiveThreads::class)->everyTenMinutes();

        $schedule->job(Jobs\UpdateAllegroDisputes::class)->everyFiveMinutes();
        $schedule->job(Jobs\GetNewAllegroDisputesJob::class)->everyFiveMinutes();

        // i am changing it from everyMinute to everyThirtyMinutes as rewriting would take some time, this should solve
        // queue overload issues
        $schedule->job(Jobs\ValidateSubiekt::class)->everyFiveMinutes();
        // $schedule->job(Jobs\ChangeOrderInvoiceData::class)->dailyAt("07:00");
        // $schedule->job(Jobs\JpgGeneratorJob::class)->dailyAt("01:00");
        $schedule->job(Jobs\ImportCsvFileJob::class)->everyFiveMinutes();
//        $schedule->job(Jobs\ImportOrdersFromSelloJob::class)->cron('0 6,11,17,22 * * *');
        $schedule->job(Jobs\UpdatePackageRealCostJob::class)->dailyAt("00:30");
        // $schedule->job(Jobs\CheckIfInvoicesExistInOrders::class)->dailyAt("07:00");
        // $schedule->job(Jobs\UrgentInvoiceRequest::class)->everyFifteenMinutes()->between('9:00', '17:00');
        $schedule->job(Jobs\CheckForHangedChats::class)->cron('0,15,30,45 7-17 * * 1-5');
        $schedule->job(Jobs\ConfirmSentPackagesJob::class)->dailyAt("23:34");
        $schedule->job(Jobs\AutomaticallyFinishOrdersJob::class)->everyFifteenMinutes();
//        $schedule->job(Jobs\ChangeDdpShipmentDatePackagesJob::class)->dailyAt("12:01");

        //$schedule->job(Jobs\SendMessagesOnNewAllegroOrders::class)->everyFifteenMinutes();

        // $schedule->job(Jobs\Cron\SendOrderInvoiceMsgMailsJob::class)->dailyAt("09:00");
        $schedule->job(Jobs\Cron\SendInvoicesMailsJob::class)->dailyAt("23:45");
        $schedule->command('import:allegro')->everyTwoMinutes()->between('8:00', '18:00');
        $schedule->command('import:allegro')->everyTenMinutes()->between('18:00', '8:00');

        $schedule->job(Jobs\AllegroCustomerReturnsJob::class)->hourly();
        $schedule->job(Jobs\PreferredInvoiceDateFillJob::class)->monthlyOn();

        $schedule->job(Jobs\EmailSendingJob::class)->everyTwoMinutes()->between('8:00', '18:00');
        $schedule->job(Jobs\EmailSendingJob::class)->everyTenMinutes()->between('18:00', '8:00');

        $schedule->job(Jobs\TaskTransfersJob::class)->dailyAt("01:00");

        $schedule->command('schenker:pull_package_dictionary')->daily();

        $schedule->job('set-logs-permissions')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
