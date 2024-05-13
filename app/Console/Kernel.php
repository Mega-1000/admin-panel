<?php

namespace App\Console;

use App\Console\Commands\AllegroBillingImportRestApiCommand;
use App\Console\Commands\CheckGlsPackageStatusCommand;
use App\Jobs;
use App\Jobs\SendSpeditionNotifications;
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
        CheckGlsPackageStatusCommand::class,
        AllegroBillingImportRestApiCommand::class,
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

        $schedule->job(Jobs\CheckPackagesStatusJob::class)->everyFifteenMinutes()->between('16:01', '12:59');
        $schedule->job(Jobs\CheckPackagesStatusJob::class)->everyFiveMinutes()->between('13:00', '16:00');

        $schedule->job(Jobs\SendLPWithReminderSendingToWarehouseJob::class)->dailyAt("05:00");
        $schedule->job(Jobs\CheckPriceChangesInProductsJob::class)->dailyAt("04:00");
        $schedule->job(Jobs\CheckDateOfProductNewPriceJob::class)->dailyAt("04:30");
        $schedule->job(Jobs\Orders\TriggerOrderLabelSchedulersJob::class)->everyFiveMinutes();
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
        $schedule->job(Jobs\ImportCsvFileJob::class)->everyFiveMinutes();
        $schedule->job(Jobs\UpdatePackageRealCostJob::class)->dailyAt("00:30");
        $schedule->job(Jobs\CheckForHangedChats::class)->cron('0,15,30,45 7-17 * * 1-5');
        $schedule->job(Jobs\ConfirmSentPackagesJob::class)->dailyAt("23:34");
        $schedule->job(Jobs\AutomaticallyFinishOrdersJob::class)->everyFifteenMinutes();

        $schedule->job(Jobs\Cron\SendInvoicesMailsJob::class)->dailyAt("23:45");
        $schedule->command('import:allegro')->everyTwoMinutes()->between('8:00', '18:00');
        $schedule->command('import:allegro')->everyTenMinutes()->between('18:00', '8:00');

        $schedule->job(Jobs\AllegroCustomerReturnsJob::class)->hourly();
        $schedule->job(Jobs\PreferredInvoiceDateFillJob::class)->monthlyOn();

        $schedule->command('send:emails')->everyTwoMinutes()->between('8:00', '18:00');
        $schedule->command('send:emails')->everyTenMinutes()->between('18:00', '8:00');

        $schedule->job(Jobs\TaskTransfersJob::class)->dailyAt("01:00");

        $schedule->command('schenker:pull_package_dictionary')->daily();

        $schedule->job('set-logs-permissions')->dailyAt('01:00');

        $schedule->command('import:transactions')->dailyAt('02:30');
        $schedule->job(Jobs\SendMonitoryNotesEmails::class)->everyThirtyMinutes()->between('7:00', '18:00');
        $schedule->job(Jobs\CheckDeliveryDatesJob::class)->everyThirtyMinutes()->between('7:00', '18:00');
        $schedule->job(SendSpeditionNotifications::class)->everyThirtyMinutes()->between('7:00', '18:00');
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
