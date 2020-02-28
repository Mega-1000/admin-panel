<?php

namespace App\Console;

use App\Jobs\AddNewWorkHourForUsers;
use App\Jobs\ChangeOrderInvoiceData;
use App\Jobs\ChangeShipmentDatePackagesJob;
use App\Jobs\CheckPackagesStatusJob;
use App\Jobs\CheckPriceChangesInProductsJob;
use App\Jobs\CheckPromisePaymentsDates;
use App\Jobs\CheckStatusInpostPackagesJob;
use App\Jobs\CheckTasksFromYesterdayJob;
use App\Jobs\GetAllegroOrders;
use App\Jobs\ImportCsvFileJob;
use App\Jobs\ImportCustomersJob;
use App\Jobs\ImportFirmsAndWarehousesJob;
use App\Jobs\ImportPaymentsFromPdfFile;
use App\Jobs\Orders\CloseChattingsJob;
use App\Jobs\Orders\TriggerOrderLabelSchedulersJob;
use App\Jobs\SearchOrdersInStoredMailsJob;
use App\Jobs\ValidateSubiekt;
use App\Jobs\WarehouseDispatchPendingReminderJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SendLPWithReminderSendingToWarehouseJob;
use App\Jobs\CheckDateOfProductNewPriceJob;

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
        //$schedule->command('inspire')
        //    ->hourly();
//        $schedule->call(function () {
//            dispatch_now(new ChangeOrderInvoiceData());
//        });
        //$schedule->job(ImportFirmsAndWarehousesJob::class)->everyMinute();
        //$schedule->job(ImportCsvFileJob::class)->everyMinute();
        //$schedule->job(ImportCustomersJob::class)->everyMinute();
        //$schedule->job(CloseChattingsJob::class)->dailyAt("05:10");


        $schedule->job(CheckStatusInpostPackagesJob::class)->everyMinute();
        $schedule->job(CheckPackagesStatusJob::class)->everyMinute();
        $schedule->job(ChangeShipmentDatePackagesJob::class)->dailyAt("00:30");
        $schedule->job(SendLPWithReminderSendingToWarehouseJob::class)->dailyAt("05:00");
        $schedule->job(CheckPriceChangesInProductsJob::class)->dailyAt("04:00");
        $schedule->job(CheckDateOfProductNewPriceJob::class)->dailyAt("04:30");
        $schedule->job(TriggerOrderLabelSchedulersJob::class)->everyFiveMinutes();
        $schedule->job(AddNewWorkHourForUsers::class)->dailyAt("00:01");
        $schedule->job(CheckTasksFromYesterdayJob::class)->dailyAt("00:01");
        $schedule->job(WarehouseDispatchPendingReminderJob::class)->everyFifteenMinutes()->between('9:00', '17:00');
		$schedule->job(CheckPromisePaymentsDates::class)->everyMinute();
        $schedule->job(ValidateSubiekt::class)->everyFiveMinutes();
        $schedule->job(ChangeOrderInvoiceData::class)->dailyAt("07:00");
        $schedule->job(SearchOrdersInStoredMailsJob::class)->everyFifteenMinutes();
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
