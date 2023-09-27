<?php

namespace App\Providers;

use App\Entities\OrderLabel;
use App\Entities\OrderPackage;
use App\Observers\Entities\OrderLabelsObserver;
use App\Observers\OrderPackageObserver;
use App\Repositories\FileInvoiceRepository;
use App\Repositories\InvoiceRepositoryInterface;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if (config('app.debug_query')) {
            DB::listen(function ($sql) {
                error_log($sql->sql);
            });
        }

        $forceHttpsEnvs = ['production', 'test'];

        if (in_array($this->app->environment(), $forceHttpsEnvs)) {
            URL::forceScheme('https');
        }
        // TODO Check solutions for deprecated functions
        Carbon::setWeekendDays([CarbonInterface::SUNDAY, CarbonInterface::SATURDAY]);

        OrderPackage::observe(OrderPackageObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        Schema::defaultStringLength(191);
    }
}
